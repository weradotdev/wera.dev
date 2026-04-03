<?php

namespace App\Listeners;

use App\Events\TaskAssigned;
use App\Services\TaskIntegrationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class NotifyAssigneeOnTaskAssigned implements ShouldQueue
{
    public function __construct(protected TaskIntegrationService $integration) {}

    public function handle(TaskAssigned $event): void
    {
        $task = $event->task;
        $assignee = $event->assignee;

        $task->load('project');
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

        $message = "You have been assigned to *{$task->title}* in *{$project->name}*.";

        try {
            $this->integration->notifyNewAssignees($task, [$assignee->id]);
        } catch (Throwable $exception) {
            Log::warning('NotifyAssigneeOnTaskAssigned failed.', [
                'task_id'     => $task->id,
                'assignee_id' => $assignee->id,
                'exception'   => $exception,
            ]);
        }
    }
}
