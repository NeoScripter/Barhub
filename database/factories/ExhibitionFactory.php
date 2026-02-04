<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exhibition>
 */
class ExhibitionFactory extends Factory
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
            'name' => $faker->words(random_int(4, 10), true),
            'starts_at' => $faker->date('Y-m-d'),
            'ends_at' => function (array $attributes) {
                return Carbon::parse($attributes['starts_at'])
                    ->addDays(random_int(1, 365))
                    ->format('Y-m-d');
            },
            'location' => $faker->words(random_int(2, 6), true),
            'buildin_folder_url' => $faker->url(),
            'is_active' => $faker->randomElement([true, false]),
        ];
    }
}
