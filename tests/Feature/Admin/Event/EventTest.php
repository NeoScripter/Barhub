<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Event;
use App\Models\Exhibition;
use App\Models\Stage;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

describe('Event Panel Access Control', function (): void {
    it('redirects guest users to login', function (): void {
        $exhibition = Exhibition::factory()->create();

        get(route('admin.exhibitions.events.index', $exhibition))
            ->assertRedirect(route('login'));
    });

    it('forbids USER role from accessing events page', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);
        $exhibition = Exhibition::factory()->create();

        actingAs($user)
            ->get(route('admin.exhibitions.events.index', $exhibition))
            ->assertForbidden();
    });

    it('forbids EXPONENT role from accessing events page', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::EXPONENT);
        $exhibition = Exhibition::factory()->create();

        actingAs($user)
            ->get(route('admin.exhibitions.events.index', $exhibition))
            ->assertForbidden();
    });

    test('super admin can see all events for any exhibition', function (): void {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(UserRole::SUPER_ADMIN);

        $exhibition = Exhibition::factory()->create();
        $events = Event::factory(5)->for($exhibition)->create();

        $response = actingAs($superAdmin)
            ->get(route('admin.exhibitions.events.index', $exhibition));

        $response
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('admin/Events/Index')
                    ->has('exhibition')
                    ->has('events.data', 5)
            );

        $events->each(
            fn ($event) => $response->assertSee($event->title)
        );
    });

    test('admin can see events only for exhibitions assigned to them', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        // Assigned exhibition
        $assignedExhibition = Exhibition::factory()->create();
        $assignedExhibition->users()->attach($admin);
        $assignedEvents = Event::factory(3)->for($assignedExhibition)->create();

        // Unassigned exhibition
        $unassignedExhibition = Exhibition::factory()->create();
        Event::factory(2)->for($unassignedExhibition)->create();

        actingAs($admin)
            ->get(route('admin.exhibitions.events.index', $assignedExhibition))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('admin/Events/Index')
                    ->has('events.data', 3)
            );

        actingAs($admin)
            ->get(route('admin.exhibitions.events.index', $unassignedExhibition))
            ->assertForbidden();
    });
});

describe('Event Sorting', function (): void {
    beforeEach(function (): void {
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);

        $this->exhibition = Exhibition::factory()->create();
        $this->stages = Stage::factory(3)->create();
    });

    test('events can be sorted by title ascending', function (): void {
        Event::factory()->for($this->exhibition)->create(['title' => 'Zebra Event']);
        Event::factory()->for($this->exhibition)->create(['title' => 'Alpha Event']);
        Event::factory()->for($this->exhibition)->create(['title' => 'Beta Event']);

        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.events.index', [
                'exhibition' => $this->exhibition,
                'sort' => 'title',
            ]));

        $response->assertOk();

        $events = $response->viewData('page')['props']['events']['data'];
        expect($events[0]['title'])->toBe('Alpha Event')
            ->and($events[1]['title'])->toBe('Beta Event')
            ->and($events[2]['title'])->toBe('Zebra Event');
    });

    test('events can be sorted by title descending', function (): void {
        Event::factory()->for($this->exhibition)->create(['title' => 'Zebra Event']);
        Event::factory()->for($this->exhibition)->create(['title' => 'Alpha Event']);
        Event::factory()->for($this->exhibition)->create(['title' => 'Beta Event']);

        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.events.index', [
                'exhibition' => $this->exhibition,
                'sort' => '-title',
            ]));

        $response->assertOk();

        $events = $response->viewData('page')['props']['events']['data'];
        expect($events[0]['title'])->toBe('Zebra Event')
            ->and($events[1]['title'])->toBe('Beta Event')
            ->and($events[2]['title'])->toBe('Alpha Event');
    });

    test('events can be sorted by starts_at ascending', function (): void {
        Event::factory()->for($this->exhibition)->create(['starts_at' => '2025-03-01 10:00:00']);
        Event::factory()->for($this->exhibition)->create(['starts_at' => '2025-01-01 10:00:00']);
        Event::factory()->for($this->exhibition)->create(['starts_at' => '2025-02-01 10:00:00']);

        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.events.index', [
                'exhibition' => $this->exhibition,
                'sort' => 'starts_at',
            ]));

        $response->assertOk();

        $events = $response->viewData('page')['props']['events']['data'];
        expect($events[0]['starts_at'])->toContain('2025-01-01')
            ->and($events[1]['starts_at'])->toContain('2025-02-01')
            ->and($events[2]['starts_at'])->toContain('2025-03-01');
    });

    test('events can be sorted by starts_at descending', function (): void {
        Event::factory()->for($this->exhibition)->create(['starts_at' => '2025-03-01 10:00:00']);
        Event::factory()->for($this->exhibition)->create(['starts_at' => '2025-01-01 10:00:00']);
        Event::factory()->for($this->exhibition)->create(['starts_at' => '2025-02-01 10:00:00']);

        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.events.index', [
                'exhibition' => $this->exhibition,
                'sort' => '-starts_at',
            ]));

        $response->assertOk();

        $events = $response->viewData('page')['props']['events']['data'];
        expect($events[0]['starts_at'])->toContain('2025-03-01')
            ->and($events[1]['starts_at'])->toContain('2025-02-01')
            ->and($events[2]['starts_at'])->toContain('2025-01-01');
    });

    test('events can be sorted by stage name ascending', function (): void {
        Event::factory()->for($this->exhibition)->for($this->stages[0])->create();
        Event::factory()->for($this->exhibition)->for($this->stages[1])->create();
        Event::factory()->for($this->exhibition)->for($this->stages[2])->create();

        $this->stages[0]->update(['name' => 'Zulu Stage']);
        $this->stages[1]->update(['name' => 'Alpha Stage']);
        $this->stages[2]->update(['name' => 'Bravo Stage']);

        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.events.index', [
                'exhibition' => $this->exhibition,
                'sort' => 'stage.name',
            ]));

        $response->assertOk();

        $events = $response->viewData('page')['props']['events']['data'];
        expect($events[0]['stage']['name'])->toBe('Alpha Stage')
            ->and($events[1]['stage']['name'])->toBe('Bravo Stage')
            ->and($events[2]['stage']['name'])->toBe('Zulu Stage');
    });

    test('events can be sorted by stage name descending', function (): void {
        Event::factory()->for($this->exhibition)->for($this->stages[0])->create();
        Event::factory()->for($this->exhibition)->for($this->stages[1])->create();
        Event::factory()->for($this->exhibition)->for($this->stages[2])->create();

        $this->stages[0]->update(['name' => 'Zulu Stage']);
        $this->stages[1]->update(['name' => 'Alpha Stage']);
        $this->stages[2]->update(['name' => 'Bravo Stage']);

        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.events.index', [
                'exhibition' => $this->exhibition,
                'sort' => '-stage.name',
            ]));

        $response->assertOk();

        $events = $response->viewData('page')['props']['events']['data'];
        expect($events[0]['stage']['name'])->toBe('Zulu Stage')
            ->and($events[1]['stage']['name'])->toBe('Bravo Stage')
            ->and($events[2]['stage']['name'])->toBe('Alpha Stage');
    });
});

describe('Event Search', function (): void {
    beforeEach(function (): void {
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);

        $this->exhibition = Exhibition::factory()->create();
    });

    test('events can be searched by title', function (): void {
        Event::factory()->for($this->exhibition)->create(['title' => 'Innovation Conference']);
        Event::factory()->for($this->exhibition)->create(['title' => 'Art Workshop']);
        Event::factory()->for($this->exhibition)->create(['title' => 'Tech Innovation Summit']);

        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.events.index', [
                'exhibition' => $this->exhibition,
                'search' => 'Innovation',
            ]));

        $response->assertOk();

        $events = $response->viewData('page')['props']['events']['data'];
        expect(count($events))->toBe(2);
    });
});

describe('Event Edit Page Access', function (): void {
    it('super admin can access edit page for any event', function (): void {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(UserRole::SUPER_ADMIN);

        $exhibition = Exhibition::factory()->create();
        $event = Event::factory()->for($exhibition)->create();

        actingAs($superAdmin)
            ->get(route('admin.exhibitions.events.edit', [$exhibition, $event]))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('admin/Events/Edit')
                    ->has('exhibition')
                    ->has('event')
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
            ->get(route('admin.exhibitions.events.edit', [$assignedExhibition, $assignedEvent]))
            ->assertOk();

        actingAs($admin)
            ->get(route('admin.exhibitions.events.edit', [$unassignedExhibition, $unassignedEvent]))
            ->assertForbidden();
    });
});
