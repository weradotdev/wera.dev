<?php

namespace Database\Factories;

use App\Models\Board;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Board>
 */
class BoardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->randomElement(['Backlog', 'Todo', 'In Progress', 'Review', 'Done']);

        return [
            'project_id' => Project::factory(),
            'name'       => $name,
            'color'      => $this->faker->optional()->hexColor(),
            'position'   => $this->faker->numberBetween(0, 10),
        ];
    }
}
