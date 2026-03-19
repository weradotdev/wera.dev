<?php

namespace App\Ai\Tools;

use App\Models\Project;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetProjectDetails implements Tool
{
    public function description(): Stringable|string
    {
        return 'Retrieve a project by ID: name, description, status, start and end dates.';
    }

    public function handle(Request $request): Stringable|string
    {
        $project = Project::query()->find($request['project_id']);
        if (! $project) {
            return 'Project not found.';
        }

        $data = [
            'id'          => $project->id,
            'name'        => $project->name,
            'description' => $project->description ?? '',
            'status'      => $project->status,
            'start_date'  => $project->start_date?->toDateString(),
            'end_date'    => $project->end_date?->toDateString(),
        ];

        return (string) json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'project_id' => $schema->integer()->description('The project ID.')->required(),
        ];
    }
}
