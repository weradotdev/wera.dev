<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;

class WhatsAppCommandHandler
{
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
        $lower = $rest === '' ? '' : strtolower($rest);

        if ($rest === '' || in_array($lower, ['help', 'commands', '?'], true)) {
            return $this->help();
        }

        $project = $this->resolveProject($sessionId);
        $user = $this->resolveUser($from);

        if ($project === null) {
            return "Unknown project. Session: {$sessionId}. Say *wera* for commands.";
        }

        if (str_contains($lower, 'task') && (str_contains($lower, 'how many') || str_contains($lower, 'many') || $lower === 'tasks' || $lower === 'my tasks')) {
            return $this->myTasksCount($project, $user);
        }

        if (str_contains($lower, 'overdue')) {
            return $this->overdueCount($project, $user);
        }

        if (str_contains($lower, 'project') || str_contains($lower, 'going') || str_contains($lower, 'status')) {
            return $this->projectStatus($project, $user, $rest);
        }

        return "I didn't understand. Say *wera* to see what I can do.";
    }

    public function help(): string
    {
        return <<<TEXT
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
        if ($phone === '') {
            return null;
        }

        return User::query()
            ->whereRaw("REPLACE(REPLACE(REPLACE(COALESCE(phone, ''), ' ', ''), '+', ''), '-', '') = ?", [$phone])
            ->first();
    }

    protected function myTasksCount(Project $project, ?User $user): string
    {
        if ($user === null) {
            return "I can’t find your account for this number. Add this phone in your profile to see your tasks. Say *wera* for commands.";
        }

        $count = Task::query()
            ->where('project_id', $project->id)
            ->whereHas('assignedUsers', fn ($q) => $q->where('users.id', $user->id))
            ->count();

        return "You have *{$count}* task(s) in *{$project->name}*.";
    }

    protected function overdueCount(Project $project, ?User $user): string
    {
        if ($user === null) {
            return "Add this phone in your profile to see your overdue tasks. Say *wera* for commands.";
        }

        $count = Task::query()
            ->where('project_id', $project->id)
            ->whereHas('assignedUsers', fn ($q) => $q->where('users.id', $user->id))
            ->whereNotNull('end_at')
            ->where('end_at', '<', Carbon::now())
            ->count();

        return "You have *{$count}* overdue task(s) in *{$project->name}*.";
    }

    protected function projectStatus(Project $project, ?User $user, string $message): string
    {
        if ($user !== null && ! $user->projects()->whereKey($project->id)->exists()) {
            return "You don’t have access to this project.";
        }

        $total = Task::query()->where('project_id', $project->id)->count();
        $overdue = Task::query()
            ->where('project_id', $project->id)
            ->whereNotNull('end_at')
            ->where('end_at', '<', Carbon::now())
            ->count();
        $assignedToUser = $user
            ? Task::query()
                ->where('project_id', $project->id)
                ->whereHas('assignedUsers', fn ($q) => $q->where('users.id', $user->id))
                ->count()
            : 0;

        $lines = [
            "*{$project->name}*",
            "Status: {$project->status}",
            "Total tasks: {$total}",
            "Overdue: {$overdue}",
        ];
        if ($user !== null) {
            $lines[] = "Your tasks: {$assignedToUser}";
        }

        return implode("\n", $lines);
    }
}
