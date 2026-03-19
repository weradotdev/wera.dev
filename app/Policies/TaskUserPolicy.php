<?php

namespace App\Policies;

use App\Models\TaskUser;
use App\Models\User;

class TaskUserPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, TaskUser $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, TaskUser $model): bool
    {
        return true;
    }

    public function delete(User $user, TaskUser $model): bool
    {
        return true;
    }

    public function restore(User $user, TaskUser $model): bool
    {
        return true;
    }

    public function forceDelete(User $user, TaskUser $model): bool
    {
        return true;
    }
}
