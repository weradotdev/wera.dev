<?php

namespace App\Services\Assistant;

use App\Models\AssistantActionRequest;
use App\Models\Board;
use App\Models\Task;
use InvalidArgumentException;

class AssistantActionExecutor
{
    public function execute(AssistantActionRequest $request): string
    {
        return match ($request->action) {
            'create_task' => $this->createTask($request),
            default       => throw new InvalidArgumentException("Unknown action: {$request->action}"),
        };
    }

    protected function createTask(AssistantActionRequest $request): string
    {
        $project = $request->project;
        $params = $request->parameters;
        $title = trim($params['title'] ?? '');

        if (blank($title)) {
            return 'Task title is missing. Please try again.';
        }

        // Use the first board (typically "Pending") as the default landing board.
        $board = Board::query()
            ->where('project_id', $project->id)
            ->orderBy('position')
            ->first();

        $task = Task::query()->create([
            'workspace_id' => $project->workspace_id,
            'project_id'   => $project->id,
            'board_id'     => $board?->id,
            'user_id'      => $request->user_id,
            'title'        => $title,
        ]);

        return "Task *{$task->title}* created successfully.";
    }
}
