<?php

namespace App\Observers;

use App\Models\Task;
use App\Services\TaskIntegrationService;

class TaskObserver
{
    public function __construct(
        protected TaskIntegrationService $integration
    ) {}

    public function created(Task $task): void
    {
        $task->load('project');
        $project = $task->project;
        if ($project) {
            $this->integration->createGitHubIssueIfEnabled($project, $task);
            $this->integration->notifyAssignedUsers($task);
        }
    }

    public function updated(Task $task): void
    {
        //
    }

    public function deleted(Task $task): void
    {
        //
    }

    public function restored(Task $task): void
    {
        //
    }

    public function forceDeleted(Task $task): void
    {
        //
    }
}
