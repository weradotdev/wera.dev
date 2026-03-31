<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Orion\Http\Controllers\Controller;

class ProjectController extends Controller
{
    protected $model = Project::class;

    public function exposedScopes(): array
    {
        return ['forUser'];
    }

    public function filterableBy(): array
    {
        return [
            'id', 'workspace_id', 'user_id', 'name', 'slug', 'status', 'color',
            'start_date', 'end_date', 'created_at', 'updated_at',
            'workspace.id', 'creator.id',
        ];
    }

    public function sortableBy(): array
    {
        return [
            'id', 'workspace_id', 'user_id', 'name', 'slug', 'status',
            'start_date', 'end_date', 'created_at', 'updated_at',
        ];
    }

    public function searchableBy(): array
    {
        return ['name', 'description'];
    }

    public function includes(): array
    {
        return ['workspace', 'creator', 'boards', 'tasks', 'tickets', 'users', 'projectUsers', 'plans'];
    }

    public function alwaysIncludes(): array
    {
        return ['creator', 'users'];
    }

    public function aggregates(): array
    {
        return ['workspace', 'creator', 'boards', 'tasks', 'tickets', 'users', 'projectUsers', 'plans'];
    }
}
