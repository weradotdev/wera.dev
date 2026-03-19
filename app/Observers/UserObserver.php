<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Str;

class UserObserver
{
    public function created(User $user): void
    {
        $workspace = Workspace::create([
            'name' => "{$user->name}'s Workspace",
            'slug' => $this->uniqueSlugForUser($user),
        ]);

        $workspace->users()->attach($user->id, ['role' => 'owner']);
    }

    private function uniqueSlugForUser(User $user): string
    {
        $base = Str::slug($user->name ?: 'user');
        $slug = $base;
        $suffix = uniqid();

        while (Workspace::where('slug', $slug)->exists()) {
            $suffix++;
            $slug = $base.'-'.$suffix;
        }

        return $slug;
    }
}
