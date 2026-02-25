<?php

declare(strict_types=1);

use App\Enums\PersonRole;
use App\Enums\UserRole;
use App\Models\Event;
use App\Models\Exhibition;
use App\Models\Person;
use App\Models\Stage;
use App\Models\Theme;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;
use function Pest\Laravel\delete;

describe('Event CRUD - Access Control', function (): void {
    beforeEach(function (): void {
        $this->exhibition = Exhibition::factory()->create();
        $this->event = Event::factory()->for($this->exhibition)->create();
    });

    it('redirects guest users to login on index', function (): void {
        get(route('admin.exhibitions.events.index', $this->exhibition))
            ->assertRedirect(route('login'));
    });

    it('redirects guest users to login on create', function (): void {
        get(route('admin.exhibitions.events.create', $this->exhibition))
            ->assertRedirect(route('login'));
    });

    it('redirects guest users to login on edit', function (): void {
        get(route('admin.exhibitions.events.edit', [$this->exhibition, $this->event]))
            ->assertRedirect(route('login'));
    });

    it('forbids USER role from accessing events', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        actingAs($user)
            ->get(route('admin.exhibitions.events.index', $this->exhibition))
            ->assertForbidden();
    });

    it('forbids EXPONENT role from accessing events', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::EXPONENT);

        actingAs($user)
            ->get(route('admin.exhibitions.events.index', $this->exhibition))
            ->assertForbidden();
    });

    test('super admin can access all exhibitions events', function (): void {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(UserRole::SUPER_ADMIN);

        actingAs($superAdmin)
            ->get(route('admin.exhibitions.events.index', $this->exhibition))
            ->assertOk();
    });

    test('admin can only access assigned exhibitions events', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        $assignedExhibition = Exhibition::factory()->create();
        $assignedExhibition->users()->attach($admin);

        $unassignedExhibition = Exhibition::factory()->create();

        actingAs($admin)
            ->get(route('admin.exhibitions.events.index', $assignedExhibition))
            ->assertOk();

        actingAs($admin)
            ->get(route('admin.exhibitions.events.index', $unassignedExhibition))
            ->assertForbidden();
    });
});

describe('Event Index', function (): void {
    beforeEach(function (): void {
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
    });

    test('displays all events for an exhibition', function (): void {
        $stage = Stage::factory()->create();
        $events = Event::factory(5)->for($this->exhibition)->for($stage)->create();

        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.events.index', $this->exhibition));

        $response->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->component('admin/Events/Index')
                    ->has('events.data', 5)
            );
    });

    test('eager loads relationships', function (): void {
        $stage = Stage::factory()->create();
        $theme = Theme::factory()->create();
        $person = Person::factory()->create();

        $event = Event::factory()
            ->for($this->exhibition)
            ->for($stage)
            ->create();

        $event->themes()->attach($theme);
        $event->people()->attach($person->id, ['role' => PersonRole::SPEAKER->value]);

        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.events.index', $this->exhibition));

        $response->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->has('events.data.0.stage')
                    ->has('events.data.0.themes')
                    ->has('events.data.0.people')
            );
    });

    test('searches events by title', function (): void {
        $stage = Stage::factory()->create();
        Event::factory()->for($this->exhibition)->for($stage)->create(['title' => 'Innovation Conference']);
        Event::factory()->for($this->exhibition)->for($stage)->create(['title' => 'Art Workshop']);
        Event::factory()->for($this->exhibition)->for($stage)->create(['title' => 'Tech Innovation']);

        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.events.index', [
                'exhibition' => $this->exhibition,
                'search' => 'Innovation'
            ]));

        $response->assertOk();
        $events = $response->viewData('page')['props']['events']['data'];
        expect(count($events))->toBe(2);
    });

    test('sorts events by title ascending', function (): void {
        $stage = Stage::factory()->create();
        Event::factory()->for($this->exhibition)->for($stage)->create(['title' => 'Zebra']);
        Event::factory()->for($this->exhibition)->for($stage)->create(['title' => 'Alpha']);
        Event::factory()->for($this->exhibition)->for($stage)->create(['title' => 'Beta']);

        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.events.index', [
                'exhibition' => $this->exhibition,
                'sort' => 'title'
            ]));

        $response->assertOk();
        $events = $response->viewData('page')['props']['events']['data'];
        expect($events[0]['title'])->toBe('Alpha')
            ->and($events[1]['title'])->toBe('Beta')
            ->and($events[2]['title'])->toBe('Zebra');
    });

    test('sorts events by starts_at descending', function (): void {
        $stage = Stage::factory()->create();
        Event::factory()->for($this->exhibition)->for($stage)->create(['starts_at' => '2025-03-01 10:00:00']);
        Event::factory()->for($this->exhibition)->for($stage)->create(['starts_at' => '2025-01-01 10:00:00']);
        Event::factory()->for($this->exhibition)->for($stage)->create(['starts_at' => '2025-02-01 10:00:00']);

        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.events.index', [
                'exhibition' => $this->exhibition,
                'sort' => '-starts_at'
            ]));

        $response->assertOk();
        $events = $response->viewData('page')['props']['events']['data'];
        expect($events[0]['starts_at'])->toContain('2025-03-01');
    });

    test('sorts events by stage name', function (): void {
        $stageZ = Stage::factory()->create(['name' => 'Zulu Stage']);
        $stageA = Stage::factory()->create(['name' => 'Alpha Stage']);

        Event::factory()->for($this->exhibition)->for($stageZ)->create();
        Event::factory()->for($this->exhibition)->for($stageA)->create();

        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.events.index', [
                'exhibition' => $this->exhibition,
                'sort' => 'stage.name'
            ]));

        $response->assertOk();
        $events = $response->viewData('page')['props']['events']['data'];
        expect($events[0]['stage']['name'])->toBe('Alpha Stage');
    });

    test('paginates events', function (): void {
        $stage = Stage::factory()->create();
        Event::factory(20)->for($this->exhibition)->for($stage)->create();

        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.events.index', $this->exhibition));

        $response->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->has('events.data', 15) // Default pagination
                    ->has('events.links')
            );
    });
});

describe('Event Create', function (): void {
    beforeEach(function (): void {
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
    });

    test('displays create form', function (): void {
        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.events.create', $this->exhibition));

        $response->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->component('admin/Events/Create')
                    ->has('exhibition')
                    ->has('stages')
                    ->has('themes')
                    ->has('availablePeople')
                    ->has('roles')
            );
    });

    test('provides all necessary data for form', function (): void {
        Stage::factory(3)->create();
        Theme::factory(5)->create();
        Person::factory(10)->create();

        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.events.create', $this->exhibition));

        $response->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('stages', fn($stages) => count($stages) === 3)
                    ->where('themes', fn($themes) => count($themes) === 5)
                    ->where('availablePeople', fn($people) => count($people) === 10)
                    ->where('roles', fn($roles) => count($roles) === 5)
            );
    });
});

describe('Event Store', function (): void {
    beforeEach(function (): void {
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
        $this->stage = Stage::factory()->create();
    });

    test('creates event with basic data', function (): void {
        $data = [
            'title' => 'Test Event',
            'description' => 'This is a test event description',
            'stage_id' => $this->stage->id,
            'starts_at' => '2025-06-01 10:00:00',
            'ends_at' => '2025-06-01 12:00:00',
        ];

        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.events.store', $this->exhibition), $data)
            ->assertRedirect(route('admin.exhibitions.events.index', $this->exhibition));

        assertDatabaseHas('events', [
            'title' => 'Test Event',
            'exhibition_id' => $this->exhibition->id,
            'stage_id' => $this->stage->id,
        ]);
    });

    test('creates event with themes', function (): void {
        $themes = Theme::factory(3)->create();

        $data = [
            'title' => 'Test Event',
            'description' => 'This is a test event description',
            'stage_id' => $this->stage->id,
            'starts_at' => '2025-06-01 10:00:00',
            'ends_at' => '2025-06-01 12:00:00',
            'theme_ids' => $themes->pluck('id')->toArray(),
        ];

        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.events.store', $this->exhibition), $data);

        $event = Event::where('title', 'Test Event')->first();
        expect($event->themes)->toHaveCount(3);
    });

    test('creates event with people and roles', function (): void {
        $person1 = Person::factory()->create();
        $person2 = Person::factory()->create();

        $data = [
            'title' => 'Test Event',
            'description' => 'This is a test event description',
            'stage_id' => $this->stage->id,
            'starts_at' => '2025-06-01 10:00:00',
            'ends_at' => '2025-06-01 12:00:00',
            'people' => [
                ['person_id' => $person1->id, 'roles' => [PersonRole::SPEAKER->value, PersonRole::HOST->value]],
                ['person_id' => $person2->id, 'roles' => [PersonRole::ORGANIZER->value]],
            ],
        ];

        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.events.store', $this->exhibition), $data);

        $event = Event::where('title', 'Test Event')->first();

        assertDatabaseHas('event_person', [
            'event_id' => $event->id,
            'person_id' => $person1->id,
            'role' => PersonRole::SPEAKER->value,
        ]);

        assertDatabaseHas('event_person', [
            'event_id' => $event->id,
            'person_id' => $person1->id,
            'role' => PersonRole::HOST->value,
        ]);

        assertDatabaseHas('event_person', [
            'event_id' => $event->id,
            'person_id' => $person2->id,
            'role' => PersonRole::ORGANIZER->value,
        ]);
    });

    test('creates event without optional fields', function (): void {
        $data = [
            'title' => 'Minimal Event',
            'description' => 'This is a minimal event',
            'starts_at' => '2025-06-01 10:00:00',
            'ends_at' => '2025-06-01 12:00:00',
        ];

        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.events.store', $this->exhibition), $data)
            ->assertRedirect();

        assertDatabaseHas('events', [
            'title' => 'Minimal Event',
            'stage_id' => null,
        ]);
    });

    test('validates required fields', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.events.store', $this->exhibition), [])
            ->assertSessionHasErrors(['title', 'description', 'starts_at', 'ends_at']);
    });

    test('validates title length', function (): void {
        $data = [
            'title' => str_repeat('a', 256),
            'description' => 'Valid description',
            'starts_at' => '2025-06-01 10:00:00',
            'ends_at' => '2025-06-01 12:00:00',
        ];

        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.events.store', $this->exhibition), $data)
            ->assertSessionHasErrors('title');
    });

    test('validates description length', function (): void {
        $data = [
            'title' => 'Valid Title',
            'description' => 'Short',
            'starts_at' => '2025-06-01 10:00:00',
            'ends_at' => '2025-06-01 12:00:00',
        ];

        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.events.store', $this->exhibition), $data)
            ->assertSessionHasErrors('description');
    });

    test('validates end time is after start time', function (): void {
        $data = [
            'title' => 'Test Event',
            'description' => 'This is a test event',
            'starts_at' => '2025-06-01 12:00:00',
            'ends_at' => '2025-06-01 10:00:00',
        ];

        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.events.store', $this->exhibition), $data)
            ->assertSessionHasErrors('ends_at');
    });

    test('validates stage exists', function (): void {
        $data = [
            'title' => 'Test Event',
            'description' => 'This is a test event',
            'stage_id' => 99999,
            'starts_at' => '2025-06-01 10:00:00',
            'ends_at' => '2025-06-01 12:00:00',
        ];

        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.events.store', $this->exhibition), $data)
            ->assertSessionHasErrors('stage_id');
    });

    test('validates person exists', function (): void {
        $data = [
            'title' => 'Test Event',
            'description' => 'This is a test event',
            'starts_at' => '2025-06-01 10:00:00',
            'ends_at' => '2025-06-01 12:00:00',
            'people' => [
                ['person_id' => 99999, 'roles' => [1]],
            ],
        ];

        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.events.store', $this->exhibition), $data)
            ->assertSessionHasErrors('people.0.person_id');
    });

    test('validates person has at least one role', function (): void {
        $person = Person::factory()->create();

        $data = [
            'title' => 'Test Event',
            'description' => 'This is a test event',
            'starts_at' => '2025-06-01 10:00:00',
            'ends_at' => '2025-06-01 12:00:00',
            'people' => [
                ['person_id' => $person->id, 'roles' => []],
            ],
        ];

        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.events.store', $this->exhibition), $data)
            ->assertSessionHasErrors('people.0.roles');
    });

    test('validates role values are valid', function (): void {
        $person = Person::factory()->create();

        $data = [
            'title' => 'Test Event',
            'description' => 'This is a test event',
            'starts_at' => '2025-06-01 10:00:00',
            'ends_at' => '2025-06-01 12:00:00',
            'people' => [
                ['person_id' => $person->id, 'roles' => [99]],
            ],
        ];

        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.events.store', $this->exhibition), $data)
            ->assertSessionHasErrors('people.0.roles.0');
    });
});

describe('Event Edit', function (): void {
    beforeEach(function (): void {
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
        $this->event = Event::factory()->for($this->exhibition)->create();
    });

    test('displays edit form', function (): void {
        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.events.edit', [$this->exhibition, $this->event]));

        $response->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->component('admin/Events/Edit')
                    ->has('exhibition')
                    ->has('event')
                    ->has('eventPeople')
                    ->has('stages')
                    ->has('themes')
                    ->has('availablePeople')
                    ->has('roles')
            );
    });

    test('formats event people with roles correctly', function (): void {
        $person = Person::factory()->create();
        $this->event->people()->attach($person->id, ['role' => PersonRole::SPEAKER->value]);
        $this->event->people()->attach($person->id, ['role' => PersonRole::HOST->value]);

        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.events.edit', [$this->exhibition, $this->event]));

        $response->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('eventPeople.0.person_id', $person->id)
                    ->where('eventPeople.0.roles', fn($roles) => count($roles) === 2)
            );
    });
});

describe('Event Update', function (): void {
    beforeEach(function (): void {
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
        $this->event = Event::factory()->for($this->exhibition)->create([
            'title' => 'Original Title',
        ]);
    });

    test('updates basic event data', function (): void {
        $newStage = Stage::factory()->create();

        $data = [
            'title' => 'Updated Title',
            'description' => 'Updated description text here',
            'stage_id' => $newStage->id,
            'starts_at' => '2025-07-01 14:00:00',
            'ends_at' => '2025-07-01 16:00:00',
        ];

        actingAs($this->superAdmin)
            ->put(route('admin.exhibitions.events.update', [$this->exhibition, $this->event]), $data)
            ->assertRedirect();

        assertDatabaseHas('events', [
            'id' => $this->event->id,
            'title' => 'Updated Title',
            'stage_id' => $newStage->id,
        ]);
    });

    test('updates event themes', function (): void {
        $oldThemes = Theme::factory(2)->create();
        $newThemes = Theme::factory(3)->create();

        $this->event->themes()->attach($oldThemes->pluck('id'));

        $data = [
            'title' => $this->event->title,
            'description' => $this->event->description,
            'starts_at' => $this->event->starts_at,
            'ends_at' => $this->event->ends_at,
            'theme_ids' => $newThemes->pluck('id')->toArray(),
        ];

        actingAs($this->superAdmin)
            ->put(route('admin.exhibitions.events.update', [$this->exhibition, $this->event]), $data);

        $this->event->refresh();
        expect($this->event->themes)->toHaveCount(3);
        expect($this->event->themes->pluck('id')->toArray())->toBe($newThemes->pluck('id')->toArray());
    });

    test('updates event people and roles', function (): void {
        $oldPerson = Person::factory()->create();
        $newPerson = Person::factory()->create();

        $this->event->people()->attach($oldPerson->id, ['role' => PersonRole::SPEAKER->value]);

        $data = [
            'title' => $this->event->title,
            'description' => $this->event->description,
            'starts_at' => $this->event->starts_at,
            'ends_at' => $this->event->ends_at,
            'people' => [
                ['person_id' => $newPerson->id, 'roles' => [PersonRole::HOST->value, PersonRole::ORGANIZER->value]],
            ],
        ];

        actingAs($this->superAdmin)
            ->put(route('admin.exhibitions.events.update', [$this->exhibition, $this->event]), $data);

        assertDatabaseMissing('event_person', [
            'event_id' => $this->event->id,
            'person_id' => $oldPerson->id,
        ]);

        assertDatabaseHas('event_person', [
            'event_id' => $this->event->id,
            'person_id' => $newPerson->id,
            'role' => PersonRole::HOST->value,
        ]);

        assertDatabaseHas('event_person', [
            'event_id' => $this->event->id,
            'person_id' => $newPerson->id,
            'role' => PersonRole::ORGANIZER->value,
        ]);
    });

    test('removes all people when people array is empty', function (): void {
        $person = Person::factory()->create();
        $this->event->people()->attach($person->id, ['role' => PersonRole::SPEAKER->value]);

        $data = [
            'title' => $this->event->title,
            'description' => $this->event->description,
            'starts_at' => $this->event->starts_at,
            'ends_at' => $this->event->ends_at,
            'people' => [],
        ];

        actingAs($this->superAdmin)
            ->put(route('admin.exhibitions.events.update', [$this->exhibition, $this->event]), $data);

        assertDatabaseMissing('event_person', [
            'event_id' => $this->event->id,
        ]);
    });

    test('allows same person with multiple roles', function (): void {
        $person = Person::factory()->create();

        $data = [
            'title' => $this->event->title,
            'description' => $this->event->description,
            'starts_at' => $this->event->starts_at,
            'ends_at' => $this->event->ends_at,
            'people' => [
                ['person_id' => $person->id, 'roles' => [PersonRole::SPEAKER->value, PersonRole::HOST->value, PersonRole::ORGANIZER->value]],
            ],
        ];

        actingAs($this->superAdmin)
            ->put(route('admin.exhibitions.events.update', [$this->exhibition, $this->event]), $data);

        $pivotRecords = DB::table('event_person')
            ->where('event_id', $this->event->id)
            ->where('person_id', $person->id)
            ->get();

        expect($pivotRecords)->toHaveCount(3);
    });

    test('validates update data', function (): void {
        actingAs($this->superAdmin)
            ->put(route('admin.exhibitions.events.update', [$this->exhibition, $this->event]), [
                'title' => '',
            ])
            ->assertSessionHasErrors('title');
    });
});

describe('Event Destroy', function (): void {
    beforeEach(function (): void {
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
        $this->event = Event::factory()->for($this->exhibition)->create();
    });

    test('deletes event', function (): void {
        actingAs($this->superAdmin)
            ->delete(route('admin.exhibitions.events.destroy', [$this->exhibition, $this->event]))
            ->assertRedirect();

        assertDatabaseMissing('events', [
            'id' => $this->event->id,
        ]);
    });

    test('cascades delete to event_person pivot', function (): void {
        $person = Person::factory()->create();
        $this->event->people()->attach($person->id, ['role' => PersonRole::SPEAKER->value]);

        actingAs($this->superAdmin)
            ->delete(route('admin.exhibitions.events.destroy', [$this->exhibition, $this->event]));

        assertDatabaseMissing('event_person', [
            'event_id' => $this->event->id,
        ]);
    });

    test('cascades delete to event_theme pivot', function (): void {
        $theme = Theme::factory()->create();
        $this->event->themes()->attach($theme);

        actingAs($this->superAdmin)
            ->delete(route('admin.exhibitions.events.destroy', [$this->exhibition, $this->event]));

        assertDatabaseMissing('event_theme', [
            'event_id' => $this->event->id,
        ]);
    });

    test('does not delete associated people', function (): void {
        $person = Person::factory()->create();
        $this->event->people()->attach($person->id, ['role' => PersonRole::SPEAKER->value]);

        actingAs($this->superAdmin)
            ->delete(route('admin.exhibitions.events.destroy', [$this->exhibition, $this->event]));

        assertDatabaseHas('people', [
            'id' => $person->id,
        ]);
    });

    test('does not delete associated themes', function (): void {
        $theme = Theme::factory()->create();
        $this->event->themes()->attach($theme);

        actingAs($this->superAdmin)
            ->delete(route('admin.exhibitions.events.destroy', [$this->exhibition, $this->event]));

        assertDatabaseHas('themes', [
            'id' => $theme->id,
        ]);
    });

    test('admin can only delete events from assigned exhibitions', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        $assignedExhibition = Exhibition::factory()->create();
        $assignedExhibition->users()->attach($admin);
        $assignedEvent = Event::factory()->for($assignedExhibition)->create();

        $unassignedExhibition = Exhibition::factory()->create();
        $unassignedEvent = Event::factory()->for($unassignedExhibition)->create();

        actingAs($admin)
            ->delete(route('admin.exhibitions.events.destroy', [$assignedExhibition, $assignedEvent]))
            ->assertRedirect();

        actingAs($admin)
            ->delete(route('admin.exhibitions.events.destroy', [$unassignedExhibition, $unassignedEvent]))
            ->assertForbidden();

        assertDatabaseMissing('events', ['id' => $assignedEvent->id]);
        assertDatabaseHas('events', ['id' => $unassignedEvent->id]);
    });
});

describe('Event Edge Cases', function (): void {
    beforeEach(function (): void {
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
    });

    test('handles event without stage', function (): void {
        $event = Event::factory()->for($this->exhibition)->create(['stage_id' => null]);

        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.events.index', $this->exhibition));

        $response->assertOk();
    });

    test('handles event without themes', function (): void {
        $event = Event::factory()->for($this->exhibition)->create();

        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.events.index', $this->exhibition));

        $response->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('events.data.0.themes', fn($themes) => count($themes) === 0)
            );
    });

    test('handles event without people', function (): void {
        $event = Event::factory()->for($this->exhibition)->create();

        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.events.index', $this->exhibition));

        $response->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('events.data.0.people', fn($people) => count($people) === 0)
            );
    });

    test('handles exhibition with no events', function (): void {
        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.events.index', $this->exhibition));

        $response->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('events.data', fn($events) => count($events) === 0)
            );
    });

    test('handles very long event title at boundary', function (): void {
        $data = [
            'title' => str_repeat('a', 255),
            'description' => 'Valid description here',
            'starts_at' => '2025-06-01 10:00:00',
            'ends_at' => '2025-06-01 12:00:00',
        ];

        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.events.store', $this->exhibition), $data)
            ->assertRedirect();

        assertDatabaseHas('events', [
            'title' => str_repeat('a', 255),
        ]);
    });
});
