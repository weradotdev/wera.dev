<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Orion\Http\Controllers\Controller;

class BoardController extends Controller
{
    protected $model = Board::class;

    public function exposedScopes(): array
    {
        return ['forProject'];
    }

    public function filterableBy(): array
    {
        return ['id', 'project_id', 'name', 'color', 'position', 'created_at', 'updated_at'];
    }

    public function sortableBy(): array
    {
        return ['id', 'project_id', 'name', 'position', 'created_at', 'updated_at'];
    }

    public function searchableBy(): array
    {
        return ['name'];
    }

    public function includes(): array
    {
        return ['project', 'tasks'];
    }
}
