<?php

namespace App\Ai\Tools;

use App\Models\Task;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetProjectTasks implements Tool
{
    public function description(): Stringable|string
    {
        return 'List tasks for a project. Optional: limit count and filter by priority (low, medium, high).';
    }

    public function handle(Request $request): Stringable|string
    {
        $query = Task::query()
            ->where('project_id', $request['project_id'])
            ->with('assignedUsers:id,name')
            ->orderBy('position')
            ->orderBy('id');

        if (! empty($request['priority'])) {
            $query->where('priority', $request['priority']);
        }

        $limit = isset($request['limit']) ? (int) $request['limit'] : 100;
        $tasks = $query->limit($limit)->get();

        $data = $tasks->map(fn (Task $task): array => [
            'id'         => $task->id,
            'title'      => $task->title,
            'priority'   => $task->priority,
            'progress'   => $task->progress,
            'start_at'   => $task->start_at?->toIso8601String(),
            'end_at'     => $task->end_at?->toIso8601String(),
            'assignees'  => $task->assignedUsers->pluck('name')->all(),
        ])->all();

        return (string) json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'project_id' => $schema->integer()->description('The project ID.')->required(),
            'priority'   => $schema->string()->description('Optional: low, medium, high.'),
            'limit'     => $schema->integer()->description('Max number of tasks to return (default 100).'),
        ];
    }
}
