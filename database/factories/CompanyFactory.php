<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
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
            'public_name' => $faker->words(2, true),
            'legal_name' => $faker->words(2, true),
            'description' => $faker->words(32, true),
            'phone' => $faker->phoneNumber(),
            'email' => $faker->unique()->email(),
            'site_url' => $faker->url(),
            'instagram' => substr($faker->url(), 0, 25),
            'telegram' => substr($faker->url(), 0, 25),
            'activities' => $faker->sentence(),
            'stand_code' => $faker->numberBetween(101, 700),
            'show_on_site' => true,
            'stand_area' => $faker->numberBetween(10, 30),
            'power_kw' => $faker->numberBetween(10, 30),
            'storage_enabled' => true
        ];
    }
}
