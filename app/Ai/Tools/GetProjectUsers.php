<?php

namespace App\Ai\Tools;

use App\Models\Project;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetProjectUsers implements Tool
{
    public function description(): Stringable|string
    {
        return 'List users (team members) assigned to a project with their role.';
    }

    public function handle(Request $request): Stringable|string
    {
        $project = Project::query()->find($request['project_id']);
        if (! $project) {
            return 'Project not found.';
        }

        $users = $project->users()->get()->map(fn ($user): array => [
            'id'   => $user->id,
            'name' => $user->name,
            'role' => $user->pivot->role ?? 'member',
        ])->all();

        return (string) json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'project_id' => $schema->integer()->description('The project ID.')->required(),
        ];
    }
}
