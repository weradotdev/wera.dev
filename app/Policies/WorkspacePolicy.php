<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;

class WorkspacePolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Workspace $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Workspace $model): bool
    {
        return true;
    }

    public function delete(User $user, Workspace $model): bool
    {
        return true;
    }

    public function restore(User $user, Workspace $model): bool
    {
        return true;
    }

    public function forceDelete(User $user, Workspace $model): bool
    {
        return true;
    }
}
