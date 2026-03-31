<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PersonRole;
use App\Models\Event;
use App\Models\Exhibition;
use App\Models\Person;
use App\Models\Stage;
use Illuminate\Database\Seeder;

final class EventSeeder extends Seeder
{
    public function run(): void
    {
        $exhibitions = Exhibition::all();

        if ($exhibitions->isEmpty()) {
            $this->command->error('Missing required data. Run Exhibition seeders first.');

            return;
        }

        $exhibitions->each(
            fn(Exhibition $exhibition) => Event::factory()
                ->count(2)
                ->for($exhibition)
                ->afterCreating(function (Event $event) use ($exhibition): void {
                    $peopleCount = 2;

                    for ($i = 0; $i < $peopleCount; $i++) {
                        $person = Person::factory()
                            ->afterCreating(function (Person $person): void {
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
                            ->create();

                        collect(PersonRole::cases())
                            ->random(random_int(1, 2))
                            ->each(
                                function (PersonRole $role) use ($person, $event, $exhibition) {
                                    $event->people()->attach($person->id, ['role' => $role->value]);
                                    $exhibition->people()->syncWithoutDetaching($person->id);
                                }
                            );
                    }
                })
                ->create()
        );
    }
}
