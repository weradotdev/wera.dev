<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->sentence(3);

        return [
            'workspace_id' => Workspace::factory(),
            'user_id'      => User::factory(),
            'name'         => $name,
            'slug'         => str($name)->slug()->value(),
            'icon'         => null,
            'description'  => $this->faker->optional()->paragraph(),
            'status'       => $this->faker->randomElement(['active', 'planning', 'on_hold', 'completed']),
            'start_date'   => $this->faker->optional()->date(),
            'end_date'     => $this->faker->optional()->date(),
        ];
    }
}
