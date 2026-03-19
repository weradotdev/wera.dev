<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $defaultWorkspace = Workspace::query()->firstOrCreate(
            ['slug' => 'weradotdev'],
            [
                'name'        => 'Wera',
                'description' => 'Default workspace for administrators.',
                'color'       => '#0097b2',
            ],
        );

        $admin = User::query()->firstOrCreate([
            'email' => 'admin@wera.com',
        ], [
            'first_name' => 'Admin',
            'last_name'  => 'User',
            'phone'      => '254700000000',
            'password'   => Hash::make('password'),
            'type'       => 'admin',
        ]);

        $mauko = User::query()->firstOrCreate([
            'email' => 'maukoese@gmail.com',
        ], [
            'first_name' => 'Mauko',
            'last_name'  => 'Maunde',
            'phone'      => '254705459494',
            'password'   => Hash::make('password'),
            'type'       => 'developer',
        ]);

        $defaultWorkspace->users()->syncWithoutDetaching([
            $admin->id => ['role' => 'owner'],
            $mauko->id => ['role' => 'developer'],
        ]);

        $defaultProject = Project::firstOrCreate(
            [
                'workspace_id' => $defaultWorkspace->id,
                'slug'         => 'wera',
            ],
            [
                'name'        => 'Wera Project',
                'description' => 'Default project for the developer panel.',
                'status'      => 'active',
            ],
        );

        $defaultProject->ensureDefaultBoards();

        $defaultProject->users()->syncWithoutDetaching([
            $admin->id => ['role' => 'owner'],
            $mauko->id => ['role' => 'owner'],
        ]);
    }
}
