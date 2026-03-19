<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\TaskUser;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workspace_id'     => Workspace::factory(),
            'project_id'       => Project::factory(),
            'user_id'          => User::factory(),
            'board_id' => fn (array $attributes) => Project::find($attributes['project_id'])?->boards()->first()?->id,
            'title'            => ucfirst($this->faker->words(4, true)),
            'description'      => $this->faker->optional()->paragraph(),
            'priority'         => $this->faker->randomElement(['low', 'medium', 'high']),
            'start_at'         => $this->faker->optional()->dateTimeBetween('-2 weeks', '+2 weeks'),
            'end_at'           => $this->faker->optional()->dateTimeBetween('+2 weeks', '+6 weeks'),
            'position'         => $this->faker->numberBetween(0, 50),
        ];
    }

    public function assignedTo(User|array $users): static
    {
        return $this->afterCreating(function ($task) use ($users): void {
            $users = is_array($users) ? $users : [$users];

            foreach ($users as $user) {
                TaskUser::query()->create([
                    'task_id' => $task->id,
                    'user_id' => $user->id ?? $user,
                ]);
            }
        });
    }
}
