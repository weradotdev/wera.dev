<?php

namespace App\Policies;

use App\Models\Board;
use App\Models\User;

class BoardPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Board $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Board $model): bool
    {
        return true;
    }

    public function delete(User $user, Board $model): bool
    {
        return true;
    }

    public function restore(User $user, Board $model): bool
    {
        return true;
    }

    public function forceDelete(User $user, Board $model): bool
    {
        return true;
    }
}
