<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PersonRole;
use App\Models\Event;
use App\Models\Exhibition;
use App\Models\Person;
use App\Models\PersonRoleAssignment;
use App\Models\Stage;
use App\Models\Theme;
use Illuminate\Database\Seeder;

final class EventSeeder extends Seeder
{
    public function run(): void
    {
        $stages = Stage::all();
        $themes = Theme::all();
        $exhibitions = Exhibition::all();

        if ($stages->isEmpty() || $themes->isEmpty() || $exhibitions->isEmpty()) {
            $this->command->error('Missing required data. Run Stage, Theme, and Exhibition seeders first.');
            return;
        }

        $exhibitions->each(fn(Exhibition $exhibition) =>
            Event::factory()
                ->count(10)
                ->for($exhibition)
                ->for($stages->random())
                ->hasAttached($themes->random(rand(1, 3)))
                ->has(
                    Person::factory()
                        ->count(rand(1, 3))
                        // ->has(
                        //     PersonRoleAssignment::factory()
                        //         ->count(rand(1, 2))
                        //         ->state(fn() => ['role' => collect(PersonRole::cases())->random()->value])
                        // )
                        ->afterCreating(function (Person $person) {
                            $person->images()->createMany([
                                [
                                    'webp3x' => '/storage/people/avatar3x.webp',
                                    'webp2x' => '/storage/people/avatar2x.webp',
                                    'webp' => '/storage/people/avatar.webp',
                                    'avif3x' => '/storage/people/avatar3x.avif',
                                    'avif2x' => '/storage/people/avatar2x.avif',
                                    'avif' => '/storage/people/avatar.avif',
                                    'tiny' => '/storage/people/avatar-tiny.webp',
                                    'alt' => "{$person->name}'s avatar",
                                    'type' => 'avatar',
                                ],
                                [
                                    'webp3x' => '/storage/people/logo3x.webp',
                                    'webp2x' => '/storage/people/logo2x.webp',
                                    'webp' => '/storage/people/logo.webp',
                                    'avif3x' => '/storage/people/logo3x.avif',
                                    'avif2x' => '/storage/people/logo2x.avif',
                                    'avif' => '/storage/people/logo.avif',
                                    'tiny' => '/storage/people/logo-tiny.webp',
                                    'alt' => "{$person->name}'s logo",
                                    'type' => 'logo',
                                ],
                            ]);
                        })
                )
                ->create()
        );

        $this->command->info('Events seeded successfully!');
    }
}
