<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
final class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(rand(2, 4), true),
            'description' => fake()->words(rand(20, 30), true),
            'deadline' => fake()->dateTime(),
            'status' => rand(1, 5),
        ];
    }
}
