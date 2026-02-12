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
use Illuminate\Support\Collection;

final class EventSeeder extends Seeder
{
    private Collection $stages;

    private Collection $themes;

    private readonly Collection $personRoles;

    public function __construct()
    {
        $this->personRoles = collect(PersonRole::cases());
    }

    public function run(): void
    {
        // Retrieve all stages and themes from the database
        $this->stages = Stage::all();
        $this->themes = Theme::all();

        // Validate that we have the required data
        if ($this->stages->isEmpty()) {
            $this->command->error('No stages found. Please run StageSeeder first.');

            return;
        }

        if ($this->themes->isEmpty()) {
            $this->command->error('No themes found. Please run ThemeSeeder first.');

            return;
        }

        // Get all exhibitions
        $exhibitions = Exhibition::all();

        if ($exhibitions->isEmpty()) {
            $this->command->error('No exhibitions found. Please run ExhibitionSeeder first.');

            return;
        }

        // Create 10 events for each exhibition
        $exhibitions->each(function (Exhibition $exhibition): void {
            // $this->command->info("Creating events for exhibition: {$exhibition->name}");

            for ($i = 1; $i <= 10; $i++) {
                $this->createEventWithRelations($exhibition);
            }
        });

        $this->command->info('Events seeded successfully!');
    }

    private function createEventWithRelations(Exhibition $exhibition): void
    {
        // Create the event with a random stage
        $event = Event::factory()
            ->for($exhibition, 'exhibition')
            ->for($this->stages->random(), 'stage')
            ->create();

        // Assign 1-3 random themes to the event
        $themesCount = random_int(1, 3);
        $selectedThemes = $this->themes->random($themesCount);
        $event->themes()->attach($selectedThemes->pluck('id'));

        // Create 1-3 people for this event
        $peopleCount = random_int(1, 3);
        $people = $this->createPeopleForEvent($event, $peopleCount);

        // Ensure at least one person has the ORGANIZER role
        $this->ensureOrganizerExists($people);
    }

    private function createPeopleForEvent(Event $event, int $count): Collection
    {
        $people = collect();

        for ($i = 0; $i < $count; $i++) {
            $person = Person::factory()
                ->for($event)
                ->create();

            // Assign 1-2 random roles to each person
            $rolesCount = random_int(1, 2);
            $this->assignRolesToPerson($person, $rolesCount);

            $people->push($person);
        }

        return $people;
    }

    private function assignRolesToPerson(Person $person, int $count): void
    {
        $selectedRoles = $this->personRoles->random(min($count, $this->personRoles->count()));

        foreach ($selectedRoles as $role) {
            PersonRoleAssignment::query()->create([
                'person_id' => $person->id,
                'role' => $role->value,
            ]);
        }
    }

    private function ensureOrganizerExists(Collection $people): void
    {
        // Check if any person already has the ORGANIZER role
        $hasOrganizer = $people->contains(fn(Person $person) => $person->roleAssignments()
            ->where('role', PersonRole::ORGANIZER->value)
            ->exists());

        // If no organizer exists, assign the role to the first person
        if (! $hasOrganizer && $people->isNotEmpty()) {
            $firstPerson = $people->first();

            PersonRoleAssignment::query()->firstOrCreate([
                'person_id' => $firstPerson->id,
                'role' => PersonRole::ORGANIZER->value,
            ]);
        }
    }
}
