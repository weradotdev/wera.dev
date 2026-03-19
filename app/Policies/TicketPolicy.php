<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Ticket $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Ticket $model): bool
    {
        return true;
    }

    public function delete(User $user, Ticket $model): bool
    {
        return true;
    }

    public function restore(User $user, Ticket $model): bool
    {
        return true;
    }

    public function forceDelete(User $user, Ticket $model): bool
    {
        return true;
    }
}
