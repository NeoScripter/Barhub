<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Followup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Followup>
 */
final class FollowupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->sentences(3, true),
            'comment' => fake()->words(random_int(20, 30), true),
            'status' => random_int(1, 3),
        ];
    }
}
