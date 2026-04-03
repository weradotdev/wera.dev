<?php

namespace App\Services\Assistant;

use App\Ai\Agents\ProjectAssistantAgent;
use App\Models\AgentConversation;
use App\Models\AgentConversationMessage;
use App\Models\AssistantActionRequest;
use App\Models\Project;
use App\Models\ProjectConversation;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ProjectAssistantOrchestrator
{
    public function latestConversation(Project $project, User $user, string $channel = 'mobile'): ?ProjectConversation
    {
        $this->ensureProjectAccess($project, $user);

        return ProjectConversation::query()
            ->where('project_id', $project->id)
            ->where('user_id', $user->id)
            ->where('channel', $channel)
            ->latest('updated_at')
            ->with(['conversation.messages.user'])
            ->first();
    }

    /**
     * @return array{conversation: ProjectConversation, reply: AgentConversationMessage}
     */
    public function respond(Project $project, User $user, string $message, ?ProjectConversation $projectConversation = null, string $channel = 'mobile'): array
    {
        $this->ensureProjectAccess($project, $user);

        if (null !== $projectConversation) {
            $this->ensureConversationAccess($projectConversation, $project, $user);
        }

        if (null === $projectConversation) {
            $agentConversation = AgentConversation::query()->create([
                'user_id' => $user->id,
                'title'   => Str::limit(trim($message), 80),
            ]);

            $projectConversation = ProjectConversation::query()->create([
                'project_id'      => $project->id,
                'user_id'         => $user->id,
                'channel'         => $channel,
                'conversation_id' => $agentConversation->id,
            ]);

            $projectConversation->setRelation('conversation', $agentConversation);
        }

        $agentConversation = $projectConversation->conversation;

        $agentConversation->messages()->create([
            'user_id'      => $user->id,
            'agent'        => 'project-assistant',
            'role'         => 'user',
            'content'      => $message,
            'attachments'  => [],
            'tool_calls'   => [],
            'tool_results' => [],
            'usage'        => [],
            'meta'         => ['channel' => $channel],
        ]);

        $replyPayload = $this->buildReplyPayload($project, $user, $message, $channel, $projectConversation);

        $reply = $agentConversation->messages()->create([
            'user_id'      => null,
            'agent'        => 'project-assistant',
            'role'         => 'assistant',
            'content'      => $replyPayload['content'],
            'attachments'  => [],
            'tool_calls'   => [],
            'tool_results' => [],
            'usage'        => [],
            'meta'         => ['channel' => $channel, 'mode' => $replyPayload['mode']],
        ]);

        $projectConversation->touch();

        return [
            'conversation' => $projectConversation->load('conversation.messages.user'),
            'reply'        => $reply,
        ];
    }

    /**
     * @throws AuthorizationException
     */
    protected function ensureProjectAccess(Project $project, User $user): void
    {
        $hasAccess = (int) $project->user_id === $user->id
            || $user->projects()->whereKey($project->id)->exists();

        if (! $hasAccess) {
            throw new AuthorizationException('You do not have access to this project assistant.');
        }
    }

    /**
     * @throws AuthorizationException
     */
    protected function ensureConversationAccess(ProjectConversation $projectConversation, Project $project, User $user): void
    {
        if ((int) $projectConversation->project_id !== $project->id || (int) $projectConversation->user_id !== $user->id) {
            throw new AuthorizationException('You do not have access to this conversation.');
        }
    }

    public function confirmAction(AssistantActionRequest $request): array
    {
        if ('pending' !== $request->status || $request->isExpired()) {
            return ['content' => 'This action has already been processed or has expired.', 'mode' => 'deterministic'];
        }

        $executor = new AssistantActionExecutor;
        $resultMessage = $executor->execute($request);

        $request->update(['status' => 'approved']);

        return ['content' => $resultMessage, 'mode' => 'action'];
    }

    /**
     * @return array{content: string, mode: string}
     */
    protected function buildReplyPayload(
        Project $project,
        User $user,
        string $message,
        string $channel = 'mobile',
        ?ProjectConversation $projectConversation = null,
    ): array {
        $normalized = Str::of($message)->trim()->lower()->value();

        // 4-digit confirmation code — WhatsApp / Telegram approval flow
        if (preg_match('/^\d{4}$/', $normalized)) {
            $pending = AssistantActionRequest::query()
                ->where('project_id', $project->id)
                ->where('user_id', $user->id)
                ->where('channel', $channel)
                ->pending()
                ->where('confirmation_code', $normalized)
                ->first();

            if ($pending) {
                return $this->confirmAction($pending);
            }
        }

        // Detect "create task <title>" / "add task <title>" intent
        if (preg_match('/^(?:create|add)\s+task\s+(.+)/iu', $message, $match)) {
            $title = trim($match[1]);
            $actionReq = AssistantActionRequest::query()->create([
                'project_id'      => $project->id,
                'user_id'         => $user->id,
                'conversation_id' => $projectConversation?->id,
                'channel'         => $channel,
                'action'          => 'create_task',
                'parameters'      => ['title' => $title],
            ]);

            $code = $actionReq->confirmation_code;
            $content = "I'll create a task titled *{$title}* in *{$project->name}*.\n"
                ."Reply *{$code}* to confirm, or ignore to cancel.";

            return ['content' => $content, 'mode' => 'action-proposal'];
        }

        if ('' === $normalized || str_contains($normalized, 'help')) {
            return ['content' => $this->helpText($project), 'mode' => 'deterministic'];
        }

        if (str_contains($normalized, 'overdue')) {
            return ['content' => $this->overdueSummary($project, $user), 'mode' => 'deterministic'];
        }

        if (str_contains($normalized, 'my tasks') || str_contains($normalized, 'assigned')) {
            return ['content' => $this->assignedTasksSummary($project, $user), 'mode' => 'deterministic'];
        }

        if (str_contains($normalized, 'status') || str_contains($normalized, 'summary')) {
            return ['content' => $this->projectStatusSummary($project, $user), 'mode' => 'deterministic'];
        }

        $aiReply = $this->aiFallbackReply($project, $message);

        if (null !== $aiReply) {
            return ['content' => $aiReply, 'mode' => 'ai-fallback'];
        }

        return ['content' => $this->fallbackText($project), 'mode' => 'fallback'];
    }

    protected function helpText(Project $project): string
    {
        return implode("\n", [
            "*{$project->name} assistant*",
            'I can currently help with:',
            '1. Project status summary',
            '2. Your assigned task count',
            '3. Your overdue task count',
            '',
            'Try: project status, my tasks, or overdue.',
        ]);
    }

    protected function fallbackText(Project $project): string
    {
        return "I am now connected to *{$project->name}*, but this first version only supports project status, my tasks, and overdue queries. More assistant actions are coming.";
    }

    protected function aiFallbackReply(Project $project, string $message): ?string
    {
        if (! $this->canUseAi()) {
            return null;
        }

        try {
            $agent = new ProjectAssistantAgent($project);
            $prompt = 'User request: '.$message;
            $response = $agent->prompt($prompt);

            return trim((string) $response) ?: null;
        } catch (Throwable $exception) {
            Log::warning('ProjectAssistantAgent fallback failed.', [
                'project_id' => $project->id,
                'message'    => $message,
                'exception'  => $exception,
            ]);

            return null;
        }
    }

    protected function canUseAi(): bool
    {
        $provider = config('ai.default');
        $providerConfig = Arr::wrap(config("ai.providers.{$provider}", []));

        if (($providerConfig['driver'] ?? null) === 'ollama') {
            return true;
        }

        return filled($providerConfig['key'] ?? null);
    }

    protected function projectStatusSummary(Project $project, User $user): string
    {
        $taskQuery = Task::query()->where('project_id', $project->id);
        $total = (clone $taskQuery)->count();
        $overdue = (clone $taskQuery)
            ->whereNotNull('end_at')
            ->where('end_at', '<', Carbon::now())
            ->count();
        $assignedToUser = (clone $taskQuery)
            ->whereHas('assignedUsers', fn ($query) => $query->where('users.id', $user->id))
            ->count();

        return implode("\n", [
            "*{$project->name}*",
            "Status: {$project->status}",
            "Total tasks: {$total}",
            "Overdue tasks: {$overdue}",
            "Your tasks: {$assignedToUser}",
        ]);
    }

    protected function assignedTasksSummary(Project $project, User $user): string
    {
        $assignedCount = Task::query()
            ->where('project_id', $project->id)
            ->whereHas('assignedUsers', fn ($query) => $query->where('users.id', $user->id))
            ->count();

        return "You currently have *{$assignedCount}* assigned task(s) in *{$project->name}*.";
    }

    protected function overdueSummary(Project $project, User $user): string
    {
        $overdueCount = Task::query()
            ->where('project_id', $project->id)
            ->whereHas('assignedUsers', fn ($query) => $query->where('users.id', $user->id))
            ->whereNotNull('end_at')
            ->where('end_at', '<', Carbon::now())
            ->count();

        return "You have *{$overdueCount}* overdue task(s) in *{$project->name}*.";
    }
}
