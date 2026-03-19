<?php

namespace App\Ai\Tools;

use App\Models\Task;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetTaskDetails implements Tool
{
    public function description(): Stringable|string
    {
        return 'Get full details of a single task by ID: title, description, priority, checklist, completed items, progress, dates, assignees.';
    }

    public function handle(Request $request): Stringable|string
    {
        $task = Task::query()
            ->with('assignedUsers:id,name')
            ->find($request['task_id']);

        if (! $task) {
            return 'Task not found.';
        }

        $data = [
            'id'          => $task->id,
            'title'       => $task->title,
            'description' => $task->description ?? '',
            'priority'    => $task->priority,
            'progress'    => $task->progress,
            'checklist'   => $task->checklist ?? [],
            'completed'   => $task->completed ?? [],
            'start_at'    => $task->start_at?->toIso8601String(),
            'end_at'      => $task->end_at?->toIso8601String(),
            'assignees'   => $task->assignedUsers->map(fn ($u): array => ['id' => $u->id, 'name' => $u->name])->all(),
        ];

        return (string) json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'task_id' => $schema->integer()->description('The task ID.')->required(),
        ];
    }
}
