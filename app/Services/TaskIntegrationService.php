<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\Integrations\GitHubIntegration;
use App\Services\Integrations\SlackIntegration;
use App\Services\Integrations\TelegramIntegration;
use App\Services\Integrations\WhatsAppIntegration;
use Illuminate\Support\Facades\Mail;

/**
 * Handles task-related integrations (GitHub, Slack, Telegram, WhatsApp).
 * Channel targets (telegram chat_id, whatsapp to, slack channel) can be passed by callers.
 * For specific cases, create separate notifications and pass the correct chat/target ids for each channel via notifyUserViaChannels(..., $channelTargets).
 */
class TaskIntegrationService
{
    public function __construct(
        protected GitHubIntegration $github,
        protected SlackIntegration $slack,
        protected TelegramIntegration $telegram,
        protected WhatsAppIntegration $whatsapp
    ) {}

    public function createGitHubIssueIfEnabled(Project $project, Task $task): bool
    {
        return $this->github->createIssueForTask($project, $task);
    }

    /**
     * Notify assignees of a task via project-configured channels.
     */
    public function notifyAssignedUsers(Task $task): void
    {
        $task->load(['project', 'assignedUsers']);
        $project = $task->project;
        if (! $project) {
            return;
        }
        $settings = $project->settings;
        if (empty($settings['notifications']['notify_developer_per_task'])) {
            return;
        }
        $channels = $settings['notifications']['channels'] ?? [];
        if (empty($channels)) {
            return;
        }

        $message = $this->assignmentMessage($task);

        if (in_array('whatsapp', $channels, true) && ! empty($project->settings['whatsapp']['has_group'])) {
            $assigneeNames = $task->assignedUsers->pluck('name')->all();
            $this->whatsapp->sendToGroup($project, $message, $assigneeNames);
        }

        foreach ($task->assignedUsers as $user) {
            $this->notifyUserViaChannels($project, $user, $message, $channels);
        }
    }

    /**
     * Notify specific users (e.g. newly assigned) via project channels.
     *
     * @param array<int, int> $userIds
     */
    public function notifyNewAssignees(Task $task, array $userIds): void
    {
        if (empty($userIds)) {
            return;
        }
        $task->load(['project']);
        $project = $task->project;
        if (! $project) {
            return;
        }
        $settings = $project->settings;
        if (empty($settings['notifications']['notify_developer_per_task'])) {
            return;
        }
        $channels = $settings['notifications']['channels'] ?? [];
        if (empty($channels)) {
            return;
        }

        $message = $this->assignmentMessage($task);
        $users = User::query()->whereIn('id', $userIds)->get();

        if (in_array('whatsapp', $channels, true) && ! empty($project->settings['whatsapp']['has_group'])) {
            $assigneeNames = $users->pluck('name')->all();
            $this->whatsapp->sendToGroup($project, $message, $assigneeNames);
        }

        foreach ($users as $user) {
            $this->notifyUserViaChannels($project, $user, $message, $channels);
        }
    }

    /**
     * Broadcast a freeform message to all members of a project via configured channels.
     */
    public function broadcastToProjectMembers(Project $project, string $message): void
    {
        $channels = $project->settings['notifications']['channels'] ?? [];
        if (empty($channels)) {
            return;
        }

        foreach ($project->users as $user) {
            $this->notifyUserViaChannels($project, $user, $message, $channels);
        }
    }

    /**
     * Notify via project-configured channels. Channel targets (chat_id, phone, channel, etc.)
     * can be passed in $channelTargets; otherwise falls back to project settings or user data.
     *
     * @param array<int, string>                                          $channels
     * @param array{telegram?: string, slack?: string, whatsapp?: string} $channelTargets Optional. Pass the correct id for each channel (e.g. telegram chat_id, whatsapp phone/jid, slack channel). Separate notifications can pass these when dispatching.
     */
    protected function notifyUserViaChannels(Project $project, User $user, string $message, array $channels, array $channelTargets = []): void
    {
        if (in_array('email', $channels, true) && filled($user->email)) {
            $this->sendEmailNotification($user, $message);
        }
        if (in_array('slack', $channels, true)) {
            $slackMessage = "To: {$user->name} — {$message}";
            $channel = $channelTargets['slack'] ?? null;
            $this->slack->send($project, $slackMessage, $channel);
        }
        if (in_array('telegram', $channels, true)) {
            $chatId = $channelTargets['telegram'] ?? $project->settings['telegram']['chat_id'] ?? null;
            if (filled($chatId)) {
                $this->telegram->send($project, $chatId, $message);
            }
        }
        if (in_array('whatsapp', $channels, true)) {
            $to = $channelTargets['whatsapp'] ?? $user->phone;
            if (filled($to)) {
                $this->whatsapp->send($project, $to, $message);
            }
        }
    }

    protected function sendEmailNotification(User $user, string $body): void
    {
        try {
            Mail::raw($body, function ($message) use ($user): void {
                $message->to($user->email)->subject('Task assigned to you');
            });
        } catch (\Throwable) {
            // Log and skip
        }
    }

    protected function assignmentMessage(Task $task): string
    {
        $title = $task->title;
        $url = route('filament.developer.resources.tasks.edit', ['tenant' => $task->project_id, 'record' => $task->id]);

        return "You have been assigned to a task: {$title}\n\nView task: {$url}";
    }
}
