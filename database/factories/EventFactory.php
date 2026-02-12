<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

/**
 * @extends Factory<Event>
 */
final class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = \Faker\Factory::create('ru_RU');

        return [
            'title' => $faker->words(random_int(2, 4), true),
            'description' => $faker->words(random_int(12, 15), true),
            'starts_at' => $faker->date('Y-m-d'),
            'ends_at' => fn (array $attributes) => Date::parse($attributes['starts_at'])
                ->addDays(random_int(1, 365))
                ->format('Y-m-d'),
        ];
    }
}
