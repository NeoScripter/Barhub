<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskTemplate>
 */
class TaskTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(random_int(2, 4), true),
            'description' => fake()->words(random_int(20, 30), true),
            'deadline' => fake()->dateTime(),
            'comment' => fake()->words(random_int(20, 30), true),
        ];
    }
}
