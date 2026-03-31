<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Orion\Http\Controllers\RelationController as Controller;

class TaskUserController extends Controller
{
    protected $model = Task::class;

    protected $relation = 'taskUsers';

    public function filterableBy(): array
    {
        return ['id', 'task_id', 'user_id', 'role', 'created_at', 'updated_at', 'task.id', 'user.id'];
    }

    public function sortableBy(): array
    {
        return ['id', 'task_id', 'user_id', 'role', 'created_at', 'updated_at'];
    }

    public function searchableBy(): array
    {
        return [];
    }

    public function includes(): array
    {
        return ['task', 'user'];
    }

    public function exposedScopes(): array
    {
        return [];
    }

    public function aggregates(): array
    {
        return ['task', 'user'];
    }
}
