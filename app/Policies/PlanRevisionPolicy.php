<?php

namespace App\Policies;

use App\Models\PlanRevision;
use App\Models\User;

class PlanRevisionPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, PlanRevision $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, PlanRevision $model): bool
    {
        return true;
    }

    public function delete(User $user, PlanRevision $model): bool
    {
        return true;
    }

    public function restore(User $user, PlanRevision $model): bool
    {
        return true;
    }

    public function forceDelete(User $user, PlanRevision $model): bool
    {
        return true;
    }
}
