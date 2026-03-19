<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Project $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Project $model): bool
    {
        return true;
    }

    public function delete(User $user, Project $model): bool
    {
        return true;
    }

    public function restore(User $user, Project $model): bool
    {
        return true;
    }

    public function forceDelete(User $user, Project $model): bool
    {
        return true;
    }
}
