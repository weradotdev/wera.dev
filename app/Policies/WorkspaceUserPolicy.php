<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkspaceUser;

class WorkspaceUserPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, WorkspaceUser $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, WorkspaceUser $model): bool
    {
        return true;
    }

    public function delete(User $user, WorkspaceUser $model): bool
    {
        return true;
    }

    public function restore(User $user, WorkspaceUser $model): bool
    {
        return true;
    }

    public function forceDelete(User $user, WorkspaceUser $model): bool
    {
        return true;
    }
}
