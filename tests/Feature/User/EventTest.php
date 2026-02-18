<?php

declare(strict_types=1);

use App\Enums\PersonRole;
use App\Models\Event;
use App\Models\Exhibition;
use App\Models\Person;
use App\Models\Stage;
use App\Models\Theme;
use function Pest\Laravel\get;

describe('Public Event Index Page', function (): void {
    it('renders events index page with exhibition data', function (): void {
        $exhibition = Exhibition::factory()->create();
        $stage = Stage::factory()->create();
        Event::factory(3)->for($exhibition)->for($stage)->create();

        get(route('events.index', $exhibition))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->component('user/Events/Index')
                    ->has('exhibition')
                    ->has('events', 3)
                    ->has('themes')
                    ->has('stages')
                    ->has('days')
            );
    });

    it('displays all events for an exhibition', function (): void {
        $exhibition = Exhibition::factory()->create();
        $stage = Stage::factory()->create();
        $events = Event::factory(5)->for($exhibition)->for($stage)->create();

        $response = get(route('events.index', $exhibition));

        $response->assertOk();
        $events->each(
            fn($event) => $response->assertSee($event->title)
        );
    });

    it('includes people with roles attached to events', function (): void {
        $exhibition = Exhibition::factory()->create();
        $stage = Stage::factory()->create();
        $event = Event::factory()->for($exhibition)->for($stage)->create();
        $person = Person::factory()->create();

        $event->people()->attach($person->id, ['role' => PersonRole::HOST->value]);

        $response = get(route('events.index', $exhibition));

        $response->assertOk()
            ->assertInertia(
                fn($page) => $page->component('user/Events/Index')
                    ->has('events', 1)
                    ->where('events.0.people.0.name', $person->name)
                    ->where('events.0.people.0.role', PersonRole::HOST->label())
            );
    });

    it('returns unique themes from all events', function (): void {
        $exhibition = Exhibition::factory()->create();
        $stage = Stage::factory()->create();

        $theme1 = Theme::factory()->create(['name' => 'Technology']);
        $theme2 = Theme::factory()->create(['name' => 'Art']);

        $event1 = Event::factory()->for($exhibition)->for($stage)->create();
        $event2 = Event::factory()->for($exhibition)->for($stage)->create();

        $event1->themes()->attach([$theme1->id, $theme2->id]);
        $event2->themes()->attach($theme1->id);

        $response = get(route('events.index', $exhibition));

        $response->assertOk()
            ->assertInertia(
                fn($page) => $page->component('user/Events/Index')
                    ->has('themes', 2)
                    ->where('themes.0', 'Technology')
                    ->where('themes.1', 'Art')
            );
    });

    it('returns all unique stages', function (): void {
        $exhibition = Exhibition::factory()->create();

        $stage1 = Stage::factory()->create(['name' => 'Main Hall']);
        $stage2 = Stage::factory()->create(['name' => 'Workshop Room']);

        Event::factory()->for($exhibition)->for($stage1)->create();
        Event::factory()->for($exhibition)->for($stage2)->create();

        $response = get(route('events.index', $exhibition));

        $response->assertOk()
            ->assertInertia(
                fn($page) => $page->component('user/Events/Index')
                    ->has('stages', 2)
                    ->where('stages.0', 'Main Hall')
                    ->where('stages.1', 'Workshop Room')
            );
    });

    it('returns unique event days sorted', function (): void {
        $exhibition = Exhibition::factory()->create();
        $stage = Stage::factory()->create();

        Event::factory()->for($exhibition)->for($stage)->create(['starts_at' => '2025-03-15 14:00:00']);
        Event::factory()->for($exhibition)->for($stage)->create(['starts_at' => '2025-03-15 14:00:00']);
        Event::factory()->for($exhibition)->for($stage)->create(['starts_at' => '2025-03-16 10:00:00']);
        Event::factory()->for($exhibition)->for($stage)->create(['starts_at' => '2025-03-14 10:00:00']);

        $response = get(route('events.index', $exhibition));

        $response->assertOk()
            ->assertInertia(
                fn($page) => $page->component('user/Events/Index')
                    ->has('days', 3)
                    ->where('days.0', '2025-03-14')
                    ->where('days.1', '2025-03-15')
                    ->where('days.2', '2025-03-16')
            );
    });
});

describe('Event Filtering', function (): void {
    it('filters events by stage name', function (): void {
        $exhibition = Exhibition::factory()->create();

        $mainStage = Stage::factory()->create(['name' => 'Main Stage']);
        $sideStage = Stage::factory()->create(['name' => 'Side Stage']);

        Event::factory()->for($exhibition)->for($mainStage)->create(['title' => 'Main Event']);
        Event::factory()->for($exhibition)->for($sideStage)->create(['title' => 'Side Event']);

        $response = get(route('events.index', [
            'exhibition' => $exhibition,
            'filter' => ['stage.name' => 'Main Stage']
        ]));

        $response->assertOk()
            ->assertSee('Main Event')
            ->assertDontSee('Side Event');
    });

    it('filters events by theme name', function (): void {
        $exhibition = Exhibition::factory()->create();
        $stage = Stage::factory()->create();

        $techTheme = Theme::factory()->create(['name' => 'Technology']);
        $artTheme = Theme::factory()->create(['name' => 'Art']);

        $techEvent = Event::factory()->for($exhibition)->for($stage)->create(['title' => 'Tech Talk']);
        $artEvent = Event::factory()->for($exhibition)->for($stage)->create(['title' => 'Art Workshop']);

        $techEvent->themes()->attach($techTheme);
        $artEvent->themes()->attach($artTheme);

        $response = get(route('events.index', [
            'exhibition' => $exhibition,
            'filter' => ['themes.name' => 'Technology']
        ]));

        $response->assertOk()
            ->assertSee('Tech Talk')
            ->assertDontSee('Art Workshop');
    });

    it('filters events by start date', function (): void {
        $exhibition = Exhibition::factory()->create();
        $stage = Stage::factory()->create();

        Event::factory()->for($exhibition)->for($stage)->create([
            'title' => 'March Event',
            'starts_at' => '2025-03-15 10:00:00'
        ]);
        Event::factory()->for($exhibition)->for($stage)->create([
            'title' => 'April Event',
            'starts_at' => '2025-04-15 10:00:00'
        ]);

        $response = get(route('events.index', [
            'exhibition' => $exhibition,
            'filter' => ['starts_at' => '2025-03-15']
        ]));

        $response->assertOk()
            ->assertSee('March Event')
            ->assertDontSee('April Event');
    });

    it('filters events by multiple criteria', function (): void {
        $exhibition = Exhibition::factory()->create();

        $mainStage = Stage::factory()->create(['name' => 'Main Stage']);
        $sideStage = Stage::factory()->create(['name' => 'Side Stage']);

        $techTheme = Theme::factory()->create(['name' => 'Technology']);
        $artTheme = Theme::factory()->create(['name' => 'Art']);

        $event1 = Event::factory()->for($exhibition)->for($mainStage)->create([
            'title' => 'Main Tech Talk',
            'starts_at' => '2025-03-15 10:00:00'
        ]);
        $event1->themes()->attach($techTheme);

        $event2 = Event::factory()->for($exhibition)->for($mainStage)->create([
            'title' => 'Main Art Workshop',
            'starts_at' => '2025-03-15 10:00:00'
        ]);
        $event2->themes()->attach($artTheme);

        $event3 = Event::factory()->for($exhibition)->for($sideStage)->create([
            'title' => 'Side Tech Talk',
            'starts_at' => '2025-03-15 10:00:00'
        ]);
        $event3->themes()->attach($techTheme);

        $response = get(route('events.index', [
            'exhibition' => $exhibition,
            'filter' => [
                'stage.name' => 'Main Stage',
                'themes.name' => 'Technology'
            ]
        ]));

        $response->assertOk()
            ->assertSee('Main Tech Talk')
            ->assertDontSee('Main Art Workshop')
            ->assertDontSee('Side Tech Talk');
    });
});

describe('Public Event Show Page', function (): void {
    it('renders event show page', function (): void {
        $exhibition = Exhibition::factory()->create();
        $stage = Stage::factory()->create();
        $event = Event::factory()->for($exhibition)->for($stage)->create();

        get(route('events.show', [
            'exhibition' => $exhibition,
            'event' => $event,
        ]))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->component('user/Events/Show')
                    ->has('event')
                    ->where('event.id', $event->id)
                    ->where('event.title', $event->title)
            );
    });

    it('displays event details', function (): void {
        $exhibition = Exhibition::factory()->create();
        $stage = Stage::factory()->create();
        $event = Event::factory()->for($exhibition)->for($stage)->create([
            'title' => 'Amazing Conference',
            'description' => 'A conference about amazing things'
        ]);

        get(route('events.show', [
            'exhibition' => $exhibition,
            'event' => $event,
        ]))
            ->assertOk()
            ->assertSee('Amazing Conference')
            ->assertSee('A conference about amazing things');
    });
});

describe('Event Relationships', function (): void {
    it('includes stage information with events', function (): void {
        $exhibition = Exhibition::factory()->create();
        $stage = Stage::factory()->create(['name' => 'Grand Hall']);
        $event = Event::factory()->for($exhibition)->for($stage)->create();

        get(route('events.index', $exhibition))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->component('user/Events/Index')
                    ->where('events.0.stage.name', 'Grand Hall')
            );
    });

    it('includes themes information with events', function (): void {
        $exhibition = Exhibition::factory()->create();
        $stage = Stage::factory()->create();
        $event = Event::factory()->for($exhibition)->for($stage)->create();

        $theme1 = Theme::factory()->create(['name' => 'Innovation']);
        $theme2 = Theme::factory()->create(['name' => 'Design']);

        $event->themes()->attach([$theme1->id, $theme2->id]);

        get(route('events.index', $exhibition))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->component('user/Events/Index')
                    ->has('events.0.themes', 2)
                    ->where('events.0.themes.0.name', 'Innovation')
                    ->where('events.0.themes.1.name', 'Design')
            );
    });
});
