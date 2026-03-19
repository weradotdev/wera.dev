<?php

namespace App\Policies;

use App\Models\ProjectUser;
use App\Models\User;

class ProjectUserPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, ProjectUser $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ProjectUser $model): bool
    {
        return true;
    }

    public function delete(User $user, ProjectUser $model): bool
    {
        return true;
    }

    public function restore(User $user, ProjectUser $model): bool
    {
        return true;
    }

    public function forceDelete(User $user, ProjectUser $model): bool
    {
        return true;
    }
}
