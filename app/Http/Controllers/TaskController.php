<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Orion\Http\Controllers\Controller;

class TaskController extends Controller
{
    protected $model = Task::class;

    public function exposedScopes(): array
    {
        return ['forUser', 'forProject'];
    }

    public function filterableBy(): array
    {
        return [
            'id', 'workspace_id', 'project_id', 'user_id', 'board_id', 'ticket_id',
            'title', 'priority', 'position', 'start_at', 'end_at', 'created_at', 'updated_at',
            'workspace.id', 'project.id', 'creator.id', 'board.id',
        ];
    }

    public function sortableBy(): array
    {
        return [
            'id', 'workspace_id', 'project_id', 'user_id', 'board_id', 'title',
            'priority', 'position', 'start_at', 'end_at', 'created_at', 'updated_at',
        ];
    }

    public function searchableBy(): array
    {
        return ['title', 'description'];
    }

    public function includes(): array
    {
        return ['workspace', 'project', 'creator', 'board', 'ticket', 'assignedUsers', 'taskUsers'];
    }

    public function alwaysIncludes(): array
    {
        return ['assignedUsers', 'project', 'creator', 'board'];
    }
}
