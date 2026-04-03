<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectConversation;
use App\Services\Assistant\ProjectAssistantOrchestrator;
use App\Services\Integrations\TelegramIntegration;
use Illuminate\Support\Facades\Log;
use Throwable;

class TelegramCommandHandler
{
    public function __construct(
        protected ProjectAssistantOrchestrator $assistant,
        protected TelegramIntegration $telegram,
    ) {}

    /**
     * Handle an incoming Telegram update payload and return the reply text (for testing/logging).
     * Side-effect: sends the reply back to the chat via TelegramIntegration.
     *
     * @param array<string, mixed> $update Raw Telegram Update object
     */
    public function handle(array $update): string
    {
        $message = $update['message'] ?? $update['edited_message'] ?? null;

        if (! is_array($message) || blank($message['text'] ?? null)) {
            return '';
        }

        $text = trim((string) $message['text']);
        $chatId = (string) ($message['chat']['id'] ?? '');

        if (blank($chatId)) {
            return '';
        }

        // Strip leading "wera " prefix if present
        $command = trim((string) preg_replace('/^wera\s*/i', '', $text));

        $project = $this->resolveProject($chatId);

        if (null === $project) {
            $reply = 'Unknown project. This chat is not linked to a Wera project.';
            $this->telegram->send($this->blankProject(), $chatId, $reply);

            return $reply;
        }

        // Telegram is a per-project integration — use the project creator as the conversation owner.
        $user = $project->creator;

        if (null === $user) {
            $reply = 'This project has no owner. Please configure the project owner before using the assistant.';
            $this->telegram->send($project, $chatId, $reply);

            return $reply;
        }

        try {
            $conversation = ProjectConversation::query()
                ->where('project_id', $project->id)
                ->where('user_id', $user->id)
                ->where('channel', 'telegram')
                ->latest('updated_at')
                ->first();

            $result = $this->assistant->respond(
                project: $project,
                user: $user,
                message: blank($command) ? 'help' : $command,
                projectConversation: $conversation,
                channel: 'telegram',
            );

            $reply = $result['reply']->content;
        } catch (Throwable $exception) {
            Log::error('TelegramCommandHandler::handle failed.', [
                'chat_id'   => $chatId,
                'exception' => $exception,
            ]);
            $reply = 'An error occurred. Please try again later.';
        }

        $this->telegram->send($project, $chatId, $reply);

        return $reply;
    }

    protected function resolveProject(string $chatId): ?Project
    {
        if (blank($chatId)) {
            return null;
        }

        return Project::query()
            ->whereJsonContains('settings->telegram->chat_id', $chatId)
            ->first();
    }

    /**
     * Provide a blank project placeholder when no project is found, just for type safety.
     * The TelegramIntegration::send will silently return false for an unlinked project.
     */
    protected function blankProject(): Project
    {
        return new Project;
    }
}
