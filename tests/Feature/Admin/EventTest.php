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
use Illuminate\Support\Facades\DB;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;

describe('Event Access Control', function (): void {
    beforeEach(function (): void {
        $this->exhibition = Exhibition::factory()->create();
        $this->event = Event::factory()->for($this->exhibition)->create();
    });

    it('redirects guest users to login on index', function (): void {
        get(route('admin.events.index'))
            ->assertRedirect(route('login'));
    });

    it('redirects guest users to login on create', function (): void {
        get(route('admin.events.create'))
            ->assertRedirect(route('login'));
    });

    it('redirects guest users to login on edit', function (): void {
        get(route('admin.events.edit', [$this->exhibition, $this->event]))
            ->assertRedirect(route('login'));
    });

    it('forbids USER role from accessing events', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        actingAs($user)
            ->get(route('admin.events.index'))
            ->assertForbidden();
    });

    it('forbids EXPONENT role from accessing events', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::EXPONENT);

        actingAs($user)
            ->get(route('admin.events.index'))
            ->assertForbidden();
    });

    it('super admin can access all exhibitions events', function (): void {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(UserRole::SUPER_ADMIN);

        actingAs($superAdmin)
            ->get(route('admin.events.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->component('admin/Events/Index')
                    ->has('exhibition')
            );
    });

    it('admin can access edit page only for events in assigned exhibitions', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        $assignedExhibition = Exhibition::factory()->create();
        $assignedExhibition->users()->attach($admin);
        $assignedEvent = Event::factory()->for($assignedExhibition)->create();

        $unassignedExhibition = Exhibition::factory()->create();
        $unassignedEvent = Event::factory()->for($unassignedExhibition)->create();

        actingAs($admin)
            ->get(route('admin.events.edit', [$assignedEvent]))
            ->assertOk();

        actingAs($admin)
            ->get(route('admin.events.edit', [$unassignedEvent]))
            ->assertForbidden();
    });
});

describe('Event Index', function (): void {
    beforeEach(function (): void {
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
    });

    it('displays all events for an exhibition', function (): void {
        $stage = Stage::factory()->create();
        Event::factory(5)->for($this->exhibition)->for($stage)->create();

        actingAs($this->superAdmin)
            ->get(route('admin.events.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->component('admin/Events/Index')
                    ->has('events.data', 5)
            );
    });

    it('only shows events belonging to the given exhibition', function (): void {
        Event::factory(3)->for($this->exhibition)->create();
        $otherExhibition = Exhibition::factory()->create();
        Event::factory(4)->for($otherExhibition)->create();

        actingAs($this->superAdmin)
            ->get(route('admin.events.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->has('events.data', 3)
            );
    });

    it('eager loads relationships', function (): void {
        $stage = Stage::factory()->create();
        $theme = Theme::factory()->create();
        $person = Person::factory()->create();

        $event = Event::factory()->for($this->exhibition)->for($stage)->create();
        $event->themes()->attach($theme);
        $event->people()->attach($person->id, ['role' => PersonRole::SPEAKER->value]);

        actingAs($this->superAdmin)
            ->get(route('admin.events.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->has('events.data.0.stage')
                    ->has('events.data.0.themes')
                    ->has('events.data.0.people')
            );
    });

    it('searches events by title', function (): void {
        Event::factory()->for($this->exhibition)->create(['title' => 'Innovation Conference']);
        Event::factory()->for($this->exhibition)->create(['title' => 'Art Workshop']);
        Event::factory()->for($this->exhibition)->create(['title' => 'Tech Innovation Summit']);

        $events = actingAs($this->superAdmin)
            ->get(route('admin.events.index', [
                'exhibition' => $this->exhibition,
                'search'     => 'Innovation',
            ]))
            ->assertOk()
            ->viewData('page')['props']['events']['data'];

        expect(count($events))->toBe(2);
    });

    it('search is case-insensitive', function (): void {
        Event::factory()->for($this->exhibition)->create(['title' => 'Innovation Conference']);
        Event::factory()->for($this->exhibition)->create(['title' => 'Art Workshop']);

        $events = actingAs($this->superAdmin)
            ->get(route('admin.events.index', [
                'exhibition' => $this->exhibition,
                'search'     => 'innovation',
            ]))
            ->assertOk()
            ->viewData('page')['props']['events']['data'];

        expect(count($events))->toBe(1);
    });

    it('sorts events by title ascending', function (): void {
        Event::factory()->for($this->exhibition)->create(['title' => 'Zebra Event']);
        Event::factory()->for($this->exhibition)->create(['title' => 'Alpha Event']);
        Event::factory()->for($this->exhibition)->create(['title' => 'Beta Event']);

        $events = actingAs($this->superAdmin)
            ->get(route('admin.events.index', [
                'exhibition' => $this->exhibition,
                'sort'       => 'title',
            ]))
            ->assertOk()
            ->viewData('page')['props']['events']['data'];

        expect($events[0]['title'])->toBe('Alpha Event')
            ->and($events[1]['title'])->toBe('Beta Event')
            ->and($events[2]['title'])->toBe('Zebra Event');
    });

    it('sorts events by title descending', function (): void {
        Event::factory()->for($this->exhibition)->create(['title' => 'Zebra Event']);
        Event::factory()->for($this->exhibition)->create(['title' => 'Alpha Event']);
        Event::factory()->for($this->exhibition)->create(['title' => 'Beta Event']);

        $events = actingAs($this->superAdmin)
            ->get(route('admin.events.index', [
                'exhibition' => $this->exhibition,
                'sort'       => '-title',
            ]))
            ->assertOk()
            ->viewData('page')['props']['events']['data'];

        expect($events[0]['title'])->toBe('Zebra Event')
            ->and($events[1]['title'])->toBe('Beta Event')
            ->and($events[2]['title'])->toBe('Alpha Event');
    });

    it('sorts events by starts_at ascending', function (): void {
        Event::factory()->for($this->exhibition)->create(['starts_at' => '2025-03-01 10:00:00']);
        Event::factory()->for($this->exhibition)->create(['starts_at' => '2025-01-01 10:00:00']);
        Event::factory()->for($this->exhibition)->create(['starts_at' => '2025-02-01 10:00:00']);

        $events = actingAs($this->superAdmin)
            ->get(route('admin.events.index', [
                'exhibition' => $this->exhibition,
                'sort'       => 'starts_at',
            ]))
            ->assertOk()
            ->viewData('page')['props']['events']['data'];

        expect($events[0]['starts_at'])->toContain('2025-01-01')
            ->and($events[1]['starts_at'])->toContain('2025-02-01')
            ->and($events[2]['starts_at'])->toContain('2025-03-01');
    });

    it('sorts events by starts_at descending', function (): void {
        Event::factory()->for($this->exhibition)->create(['starts_at' => '2025-03-01 10:00:00']);
        Event::factory()->for($this->exhibition)->create(['starts_at' => '2025-01-01 10:00:00']);
        Event::factory()->for($this->exhibition)->create(['starts_at' => '2025-02-01 10:00:00']);

        $events = actingAs($this->superAdmin)
            ->get(route('admin.events.index', [
                'exhibition' => $this->exhibition,
                'sort'       => '-starts_at',
            ]))
            ->assertOk()
            ->viewData('page')['props']['events']['data'];

        expect($events[0]['starts_at'])->toContain('2025-03-01')
            ->and($events[1]['starts_at'])->toContain('2025-02-01')
            ->and($events[2]['starts_at'])->toContain('2025-01-01');
    });

    it('sorts events by stage name ascending', function (): void {
        $stageZ = Stage::factory()->create(['name' => 'Zulu Stage']);
        $stageA = Stage::factory()->create(['name' => 'Alpha Stage']);
        $stageB = Stage::factory()->create(['name' => 'Bravo Stage']);

        Event::factory()->for($this->exhibition)->for($stageZ)->create();
        Event::factory()->for($this->exhibition)->for($stageA)->create();
        Event::factory()->for($this->exhibition)->for($stageB)->create();

        $events = actingAs($this->superAdmin)
            ->get(route('admin.events.index', [
                'exhibition' => $this->exhibition,
                'sort'       => 'stage.name',
            ]))
            ->assertOk()
            ->viewData('page')['props']['events']['data'];

        expect($events[0]['stage']['name'])->toBe('Alpha Stage')
            ->and($events[1]['stage']['name'])->toBe('Bravo Stage')
            ->and($events[2]['stage']['name'])->toBe('Zulu Stage');
    });

    it('sorts events by stage name descending', function (): void {
        $stageZ = Stage::factory()->create(['name' => 'Zulu Stage']);
        $stageA = Stage::factory()->create(['name' => 'Alpha Stage']);
        $stageB = Stage::factory()->create(['name' => 'Bravo Stage']);

        Event::factory()->for($this->exhibition)->for($stageZ)->create();
        Event::factory()->for($this->exhibition)->for($stageA)->create();
        Event::factory()->for($this->exhibition)->for($stageB)->create();

        $events = actingAs($this->superAdmin)
            ->get(route('admin.events.index', [
                'exhibition' => $this->exhibition,
                'sort'       => '-stage.name',
            ]))
            ->assertOk()
            ->viewData('page')['props']['events']['data'];

        expect($events[0]['stage']['name'])->toBe('Zulu Stage')
            ->and($events[1]['stage']['name'])->toBe('Bravo Stage')
            ->and($events[2]['stage']['name'])->toBe('Alpha Stage');
    });

    it('paginates events', function (): void {
        Event::factory(20)->for($this->exhibition)->create();

        actingAs($this->superAdmin)
            ->get(route('admin.events.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->has('events.data', 15)
                    ->has('events.links')
            );
    });

    it('handles exhibition with no events', function (): void {
        actingAs($this->superAdmin)
            ->get(route('admin.events.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('events.data', fn($events): bool => count($events) === 0)
            );
    });

    it('handles event without stage', function (): void {
        Event::factory()->for($this->exhibition)->create(['stage_id' => null]);

        actingAs($this->superAdmin)
            ->get(route('admin.events.index'))
            ->assertOk();
    });

    it('handles event without themes', function (): void {
        Event::factory()->for($this->exhibition)->create();

        actingAs($this->superAdmin)
            ->get(route('admin.events.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('events.data.0.themes', fn($themes): bool => count($themes) === 0)
            );
    });

    it('handles event without people', function (): void {
        Event::factory()->for($this->exhibition)->create();

        actingAs($this->superAdmin)
            ->get(route('admin.events.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('events.data.0.people', fn($people): bool => count($people) === 0)
            );
    });
});

describe('Event Create', function (): void {
    beforeEach(function (): void {
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
        $this->stage = Stage::factory()->create();
    });

    it('displays create form', function (): void {
        actingAs($this->superAdmin)
            ->get(route('admin.events.create'))
            ->assertOk()
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

    it('provides all necessary data for form', function (): void {
        Stage::factory(3)->create();
        Theme::factory(5)->create();
        Person::factory(10)->create();

        actingAs($this->superAdmin)
            ->get(route('admin.events.create'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('stages', fn($stages): bool => count($stages) === 3)
                    ->where('themes', fn($themes): bool => count($themes) === 5)
                    ->where('availablePeople', fn($people): bool => count($people) === 10)
                    ->where('roles', fn($roles): bool => count($roles) === 5)
            );
    });

    it('creates event with basic data', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.events.store'), [
                'title'       => 'Test Event',
                'description' => 'This is a test event description',
                'stage_id'    => $this->stage->id,
                'starts_at'   => '2025-06-01 10:00:00',
                'ends_at'     => '2025-06-01 12:00:00',
            ])
            ->assertRedirect(route('admin.events.index'));

        assertDatabaseHas('events', [
            'title'          => 'Test Event',
            'exhibition_id'  => $this->exhibition->id,
            'stage_id'       => $this->stage->id,
        ]);
    });

    it('creates event with themes', function (): void {
        $themes = Theme::factory(3)->create();

        actingAs($this->superAdmin)
            ->post(route('admin.events.store'), [
                'title'       => 'Test Event',
                'description' => 'This is a test event description',
                'stage_id'    => $this->stage->id,
                'starts_at'   => '2025-06-01 10:00:00',
                'ends_at'     => '2025-06-01 12:00:00',
                'theme_ids'   => $themes->pluck('id')->toArray(),
            ]);

        $event = Event::query()->where('title', 'Test Event')->first();
        expect($event->themes)->toHaveCount(3);
    });

    it('creates event with people and roles', function (): void {
        $person1 = Person::factory()->create();
        $person2 = Person::factory()->create();

        actingAs($this->superAdmin)
            ->post(route('admin.events.store'), [
                'title'       => 'Test Event',
                'description' => 'This is a test event description',
                'stage_id'    => $this->stage->id,
                'starts_at'   => '2025-06-01 10:00:00',
                'ends_at'     => '2025-06-01 12:00:00',
                'people'      => [
                    ['person_id' => $person1->id, 'roles' => [PersonRole::SPEAKER->value, PersonRole::HOST->value]],
                    ['person_id' => $person2->id, 'roles' => [PersonRole::ORGANIZER->value]],
                ],
            ]);

        $event = Event::query()->where('title', 'Test Event')->first();

        assertDatabaseHas('event_person', ['event_id' => $event->id, 'person_id' => $person1->id, 'role' => PersonRole::SPEAKER->value]);
        assertDatabaseHas('event_person', ['event_id' => $event->id, 'person_id' => $person1->id, 'role' => PersonRole::HOST->value]);
        assertDatabaseHas('event_person', ['event_id' => $event->id, 'person_id' => $person2->id, 'role' => PersonRole::ORGANIZER->value]);
    });

    it('creates event without optional fields', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.events.store'), [
                'title'       => 'Minimal Event',
                'description' => 'This is a minimal event',
                'starts_at'   => '2025-06-01 10:00:00',
                'ends_at'     => '2025-06-01 12:00:00',
            ])
            ->assertRedirect();

        assertDatabaseHas('events', ['title' => 'Minimal Event', 'stage_id' => null]);
    });

    it('handles very long event title at boundary', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.events.store'), [
                'title'       => str_repeat('a', 255),
                'description' => 'Valid description here',
                'starts_at'   => '2025-06-01 10:00:00',
                'ends_at'     => '2025-06-01 12:00:00',
            ])
            ->assertRedirect();

        assertDatabaseHas('events', ['title' => str_repeat('a', 255)]);
    });

    it('validates required fields', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.events.store'), [])
            ->assertSessionHasErrors(['title', 'description', 'starts_at', 'ends_at']);
    });

    it('validates title length', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.events.store'), [
                'title'       => str_repeat('a', 256),
                'description' => 'Valid description',
                'starts_at'   => '2025-06-01 10:00:00',
                'ends_at'     => '2025-06-01 12:00:00',
            ])
            ->assertSessionHasErrors('title');
    });

    it('validates description length', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.events.store'), [
                'title'       => 'Valid Title',
                'description' => 'Short',
                'starts_at'   => '2025-06-01 10:00:00',
                'ends_at'     => '2025-06-01 12:00:00',
            ])
            ->assertSessionHasErrors('description');
    });

    it('validates end time is after start time', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.events.store'), [
                'title'       => 'Test Event',
                'description' => 'This is a test event',
                'starts_at'   => '2025-06-01 12:00:00',
                'ends_at'     => '2025-06-01 10:00:00',
            ])
            ->assertSessionHasErrors('ends_at');
    });

    it('validates stage exists', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.events.store'), [
                'title'       => 'Test Event',
                'description' => 'This is a test event',
                'stage_id'    => 99999,
                'starts_at'   => '2025-06-01 10:00:00',
                'ends_at'     => '2025-06-01 12:00:00',
            ])
            ->assertSessionHasErrors('stage_id');
    });

    it('validates person exists', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.events.store'), [
                'title'       => 'Test Event',
                'description' => 'This is a test event',
                'starts_at'   => '2025-06-01 10:00:00',
                'ends_at'     => '2025-06-01 12:00:00',
                'people'      => [['person_id' => 99999, 'roles' => [1]]],
            ])
            ->assertSessionHasErrors('people.0.person_id');
    });

    it('validates person has at least one role', function (): void {
        $person = Person::factory()->create();

        actingAs($this->superAdmin)
            ->post(route('admin.events.store'), [
                'title'       => 'Test Event',
                'description' => 'This is a test event',
                'starts_at'   => '2025-06-01 10:00:00',
                'ends_at'     => '2025-06-01 12:00:00',
                'people'      => [['person_id' => $person->id, 'roles' => []]],
            ])
            ->assertSessionHasErrors('people.0.roles');
    });

    it('validates role values are valid', function (): void {
        $person = Person::factory()->create();

        actingAs($this->superAdmin)
            ->post(route('admin.events.store'), [
                'title'       => 'Test Event',
                'description' => 'This is a test event',
                'starts_at'   => '2025-06-01 10:00:00',
                'ends_at'     => '2025-06-01 12:00:00',
                'people'      => [['person_id' => $person->id, 'roles' => [99]]],
            ])
            ->assertSessionHasErrors('people.0.roles.0');
    });
});

describe('Event Edit', function (): void {
    beforeEach(function (): void {
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
        $this->event = Event::factory()->for($this->exhibition)->create(['title' => 'Original Title']);
    });

    it('displays edit form', function (): void {
        actingAs($this->superAdmin)
            ->get(route('admin.events.edit', [$this->exhibition, $this->event]))
            ->assertOk()
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

    it('formats event people with roles correctly', function (): void {
        $person = Person::factory()->create();
        $this->event->people()->attach($person->id, ['role' => PersonRole::SPEAKER->value]);
        $this->event->people()->attach($person->id, ['role' => PersonRole::HOST->value]);

        actingAs($this->superAdmin)
            ->get(route('admin.events.edit', [$this->exhibition, $this->event]))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('eventPeople.0.person_id', $person->id)
                    ->where('eventPeople.0.roles', fn($roles): bool => count($roles) === 2)
            );
    });

    it('updates basic event data', function (): void {
        $newStage = Stage::factory()->create();

        actingAs($this->superAdmin)
            ->put(route('admin.events.update', [$this->exhibition, $this->event]), [
                'title'       => 'Updated Title',
                'description' => 'Updated description text here',
                'stage_id'    => $newStage->id,
                'starts_at'   => '2025-07-01 14:00:00',
                'ends_at'     => '2025-07-01 16:00:00',
            ])
            ->assertRedirect();

        assertDatabaseHas('events', [
            'id'       => $this->event->id,
            'title'    => 'Updated Title',
            'stage_id' => $newStage->id,
        ]);
    });

    it('updates event themes', function (): void {
        $oldThemes = Theme::factory(2)->create();
        $newThemes = Theme::factory(3)->create();
        $this->event->themes()->attach($oldThemes->pluck('id'));

        actingAs($this->superAdmin)
            ->put(route('admin.events.update', [$this->exhibition, $this->event]), [
                'title'       => $this->event->title,
                'description' => $this->event->description,
                'starts_at'   => $this->event->starts_at,
                'ends_at'     => $this->event->ends_at,
                'theme_ids'   => $newThemes->pluck('id')->toArray(),
            ]);

        $this->event->refresh();
        expect($this->event->themes)->toHaveCount(3)
            ->and($this->event->themes->pluck('id')->toArray())->toBe($newThemes->pluck('id')->toArray());
    });

    it('updates event people and roles', function (): void {
        $oldPerson = Person::factory()->create();
        $newPerson = Person::factory()->create();
        $this->event->people()->attach($oldPerson->id, ['role' => PersonRole::SPEAKER->value]);

        actingAs($this->superAdmin)
            ->put(route('admin.events.update', [$this->exhibition, $this->event]), [
                'title'       => $this->event->title,
                'description' => $this->event->description,
                'starts_at'   => $this->event->starts_at,
                'ends_at'     => $this->event->ends_at,
                'people'      => [
                    ['person_id' => $newPerson->id, 'roles' => [PersonRole::HOST->value, PersonRole::ORGANIZER->value]],
                ],
            ]);

        assertDatabaseMissing('event_person', ['event_id' => $this->event->id, 'person_id' => $oldPerson->id]);
        assertDatabaseHas('event_person', ['event_id' => $this->event->id, 'person_id' => $newPerson->id, 'role' => PersonRole::HOST->value]);
        assertDatabaseHas('event_person', ['event_id' => $this->event->id, 'person_id' => $newPerson->id, 'role' => PersonRole::ORGANIZER->value]);
    });

    it('removes all people when people array is empty', function (): void {
        $person = Person::factory()->create();
        $this->event->people()->attach($person->id, ['role' => PersonRole::SPEAKER->value]);

        actingAs($this->superAdmin)
            ->put(route('admin.events.update', [$this->exhibition, $this->event]), [
                'title'       => $this->event->title,
                'description' => $this->event->description,
                'starts_at'   => $this->event->starts_at,
                'ends_at'     => $this->event->ends_at,
                'people'      => [],
            ]);

        assertDatabaseMissing('event_person', ['event_id' => $this->event->id]);
    });

    it('allows same person with multiple roles', function (): void {
        $person = Person::factory()->create();

        actingAs($this->superAdmin)
            ->put(route('admin.events.update', [$this->exhibition, $this->event]), [
                'title'       => $this->event->title,
                'description' => $this->event->description,
                'starts_at'   => $this->event->starts_at,
                'ends_at'     => $this->event->ends_at,
                'people'      => [
                    ['person_id' => $person->id, 'roles' => [PersonRole::SPEAKER->value, PersonRole::HOST->value, PersonRole::ORGANIZER->value]],
                ],
            ]);

        $pivotRecords = DB::table('event_person')
            ->where('event_id', $this->event->id)
            ->where('person_id', $person->id)
            ->get();

        expect($pivotRecords)->toHaveCount(3);
    });

    it('validates update data', function (): void {
        actingAs($this->superAdmin)
            ->put(route('admin.events.update', [$this->exhibition, $this->event]), ['title' => ''])
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

    it('deletes event', function (): void {
        actingAs($this->superAdmin)
            ->delete(route('admin.events.destroy', [$this->exhibition, $this->event]))
            ->assertRedirect();

        assertDatabaseMissing('events', ['id' => $this->event->id]);
    });

    it('cascades delete to event_person pivot', function (): void {
        $person = Person::factory()->create();
        $this->event->people()->attach($person->id, ['role' => PersonRole::SPEAKER->value]);

        actingAs($this->superAdmin)
            ->delete(route('admin.events.destroy', [$this->exhibition, $this->event]));

        assertDatabaseMissing('event_person', ['event_id' => $this->event->id]);
    });

    it('cascades delete to event_theme pivot', function (): void {
        $theme = Theme::factory()->create();
        $this->event->themes()->attach($theme);

        actingAs($this->superAdmin)
            ->delete(route('admin.events.destroy', [$this->exhibition, $this->event]));

        assertDatabaseMissing('event_theme', ['event_id' => $this->event->id]);
    });

    it('does not delete associated people', function (): void {
        $person = Person::factory()->create();
        $this->event->people()->attach($person->id, ['role' => PersonRole::SPEAKER->value]);

        actingAs($this->superAdmin)
            ->delete(route('admin.events.destroy', [$this->exhibition, $this->event]));

        assertDatabaseHas('people', ['id' => $person->id]);
    });

    it('admin can only delete events from assigned exhibitions', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        $assignedExhibition = Exhibition::factory()->create();
        $assignedExhibition->users()->attach($admin);
        $assignedEvent = Event::factory()->for($assignedExhibition)->create();

        $unassignedExhibition = Exhibition::factory()->create();
        $unassignedEvent = Event::factory()->for($unassignedExhibition)->create();

        actingAs($admin)
            ->delete(route('admin.events.destroy', [$assignedEvent]))
            ->assertRedirect();

        actingAs($admin)
            ->delete(route('admin.events.destroy', [$unassignedEvent]))
            ->assertForbidden();

        assertDatabaseMissing('events', ['id' => $assignedEvent->id]);
        assertDatabaseHas('events', ['id' => $unassignedEvent->id]);
    });
});
