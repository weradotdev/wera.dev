<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectConversation;
use App\Models\User;
use App\Services\Assistant\ProjectAssistantOrchestrator;

class WhatsAppCommandHandler
{
    public function __construct(
        protected ProjectAssistantOrchestrator $assistant,
    ) {}

    /**
     * Handle an incoming WhatsApp message and return a reply.
     * Only called for messages starting with "wera" (case-insensitive).
     * session_id is e.g. "project-5", from is phone digits or JID.
     */
    public function handle(string $sessionId, string $from, string $message): string
    {
        $message = trim($message);
        $rest = preg_replace('/^wera\s*/i', '', $message);
        $rest = trim($rest);

        $project = $this->resolveProject($sessionId);
        $user = $this->resolveUser($from);

        if (null === $project) {
            return "Unknown project. Session: {$sessionId}. Say *wera* for commands.";
        }

        if (null === $user) {
            $lower = '' === $rest ? '' : strtolower($rest);

            if ('' === $rest || in_array($lower, ['help', 'commands', '?'], true)) {
                return $this->help();
            }

            return 'I can’t find your account for this number. Add this phone in your profile to use the assistant in WhatsApp. Say *wera* for commands.';
        }

        $conversation = ProjectConversation::query()
            ->where('project_id', $project->id)
            ->where('user_id', $user->id)
            ->where('channel', 'whatsapp')
            ->latest('updated_at')
            ->first();

        $result = $this->assistant->respond(
            project: $project,
            user: $user,
            message: '' === $rest ? 'help' : $rest,
            projectConversation: $conversation,
            channel: 'whatsapp',
        );

        return $result['reply']->content;
    }

    public function help(): string
    {
        return <<<'TEXT'
*Wera* – Reply only to messages that *start with wera*. Here’s what I can do:

• *wera* or *wera help* – Show this message
• *wera* how many tasks / *wera* my tasks – Your task count in this project
• *wera* overdue – Your overdue tasks in this project
• *wera* project status – Summary for this project (tasks, overdue)

_Add your phone number in your profile so I can link your tasks._
TEXT;
    }

    protected function resolveProject(string $sessionId): ?Project
    {
        if (preg_match('/^project-(\d+)$/i', $sessionId, $m)) {
            return Project::query()->find((int) $m[1]);
        }

        return null;
    }

    protected function resolveUser(string $from): ?User
    {
        $phone = preg_replace('/\D/', '', $from);
        if ('' === $phone) {
            return null;
        }

        return User::query()
            ->whereRaw("REPLACE(REPLACE(REPLACE(COALESCE(phone, ''), ' ', ''), '+', ''), '-', '') = ?", [$phone])
            ->first();
    }
}
