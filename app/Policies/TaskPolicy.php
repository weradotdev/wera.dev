<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Task $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Task $model): bool
    {
        return true;
    }

    public function delete(User $user, Task $model): bool
    {
        return true;
    }

    public function restore(User $user, Task $model): bool
    {
        return true;
    }

    public function forceDelete(User $user, Task $model): bool
    {
        return true;
    }
}
