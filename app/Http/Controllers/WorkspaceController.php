<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use Orion\Http\Controllers\Controller;

class WorkspaceController extends Controller
{
    protected $model = Workspace::class;

    public function filterableBy(): array
    {
        return ['id', 'name', 'slug', 'color', 'created_at', 'updated_at'];
    }

    public function sortableBy(): array
    {
        return ['id', 'name', 'slug', 'created_at', 'updated_at'];
    }

    public function searchableBy(): array
    {
        return ['name', 'description'];
    }

    public function includes(): array
    {
        return ['projects', 'boards', 'tasks', 'tickets', 'users', 'workspaceUsers'];
    }

    public function exposedScopes(): array
    {
        return [];
    }
}
