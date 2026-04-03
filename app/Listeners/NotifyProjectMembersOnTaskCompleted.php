<?php

namespace App\Listeners;

use App\Events\TaskCompleted;
use App\Services\TaskIntegrationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class NotifyProjectMembersOnTaskCompleted implements ShouldQueue
{
    public function __construct(protected TaskIntegrationService $integration) {}

    public function handle(TaskCompleted $event): void
    {
        $task = $event->task;
        $task->load(['project', 'assignedUsers', 'creator']);

        $project = $task->project;
        if (! $project) {
            return;
        }

        $settings = $project->settings;
        if (empty($settings['notifications']['notify_on_completion'])) {
            return;
        }

        $message = "*{$task->title}* has been marked as completed in *{$project->name}*.";

        try {
            $project->load('users');
            $this->integration->broadcastToProjectMembers($project, $message);
        } catch (Throwable $exception) {
            Log::warning('NotifyProjectMembersOnTaskCompleted failed.', [
                'task_id'   => $task->id,
                'exception' => $exception,
            ]);
        }
    }
}
