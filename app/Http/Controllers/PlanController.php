<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Orion\Http\Controllers\Controller;

class PlanController extends Controller
{
    protected $model = Plan::class;

    public function filterableBy(): array
    {
        return [
            'id', 'workspace_id', 'user_id', 'planable_type', 'planable_id',
            'name', 'created_at', 'updated_at', 'workspace.id', 'user.id',
        ];
    }

    public function sortableBy(): array
    {
        return ['id', 'workspace_id', 'user_id', 'name', 'created_at', 'updated_at'];
    }

    public function searchableBy(): array
    {
        return ['name', 'description'];
    }

    public function includes(): array
    {
        return ['planable', 'workspace', 'user', 'revisions'];
    }

    public function exposedScopes(): array
    {
        return [];
    }
}
