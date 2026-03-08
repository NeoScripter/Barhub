<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\TaskComment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaskComment>
 */
final class TaskCommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => fake()->sentences(3, true),
        ];
    }
}
