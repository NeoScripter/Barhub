<?php

declare(strict_types=1);

use App\Enums\PersonRole;
use App\Enums\UserRole;
use App\Models\Event;
use App\Models\Exhibition;
use App\Models\Person;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

describe('Person Panel Access Control', function (): void {
    it('redirects guest users to login', function (): void {
        $exhibition = Exhibition::factory()->create();

        get(route('admin.exhibitions.people.index', $exhibition))
            ->assertRedirect(route('login'));
    });

    it('forbids USER role from accessing people page', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);
        $exhibition = Exhibition::factory()->create();

        actingAs($user)
            ->get(route('admin.exhibitions.people.index', $exhibition))
            ->assertForbidden();
    });

    it('forbids EXPONENT role from accessing people page', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::EXPONENT);
        $exhibition = Exhibition::factory()->create();

        actingAs($user)
            ->get(route('admin.exhibitions.people.index', $exhibition))
            ->assertForbidden();
    });

    test('super admin can see all people for any exhibition', function (): void {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(UserRole::SUPER_ADMIN);

        $exhibition = Exhibition::factory()->create();
        $event = Event::factory()->for($exhibition)->create();

        $people = Person::factory(5)->create();
        $people->each(fn($person) => $event->people()->attach($person->id, ['role' => PersonRole::SPEAKER->value]));

        $response = actingAs($superAdmin)
            ->get(route('admin.exhibitions.people.index', $exhibition));

        $response
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->component('admin/People/Index')
                    ->has('exhibition')
                    ->has('people.data', 5)
            );

        $inertiaData = $response->viewData('page')['props']['people']['data'];
        $people->each(function ($person) use ($inertiaData) {
            expect(collect($inertiaData)->pluck('name'))->toContain($person->name);
        });
    });

    test('admin can see people only for exhibitions assigned to them', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        // Assigned exhibition
        $assignedExhibition = Exhibition::factory()->create();
        $assignedExhibition->users()->attach($admin);
        $assignedEvent = Event::factory()->for($assignedExhibition)->create();

        $assignedPeople = Person::factory(3)->create();
        $assignedPeople->each(fn($person) => $assignedEvent->people()->attach($person->id, ['role' => PersonRole::SPEAKER->value]));

        // Unassigned exhibition
        $unassignedExhibition = Exhibition::factory()->create();
        $unassignedEvent = Event::factory()->for($unassignedExhibition)->create();

        $unassignedPeople = Person::factory(2)->create();
        $unassignedPeople->each(fn($person) => $unassignedEvent->people()->attach($person->id, ['role' => PersonRole::SPEAKER->value]));

        actingAs($admin)
            ->get(route('admin.exhibitions.people.index', $assignedExhibition))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->component('admin/People/Index')
                    ->has('people.data', 3)
            );

        actingAs($admin)
            ->get(route('admin.exhibitions.people.index', $unassignedExhibition))
            ->assertForbidden();
    });
});

describe('Person Sorting', function (): void {
    beforeEach(function (): void {
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);

        $this->exhibition = Exhibition::factory()->create();
        $this->event = Event::factory()->for($this->exhibition)->create();
    });

    test('people can be sorted by name ascending', function (): void {
        $personZ = Person::factory()->create(['name' => 'Zebra Person']);
        $personA = Person::factory()->create(['name' => 'Alpha Person']);
        $personB = Person::factory()->create(['name' => 'Beta Person']);

        $this->event->people()->attach($personZ->id, ['role' => PersonRole::SPEAKER->value]);
        $this->event->people()->attach($personA->id, ['role' => PersonRole::SPEAKER->value]);
        $this->event->people()->attach($personB->id, ['role' => PersonRole::SPEAKER->value]);

        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.people.index', [
                'exhibition' => $this->exhibition,
                'sort' => 'name',
            ]));

        $response->assertOk();

        $people = $response->viewData('page')['props']['people']['data'];
        expect($people[0]['name'])->toBe('Alpha Person')
            ->and($people[1]['name'])->toBe('Beta Person')
            ->and($people[2]['name'])->toBe('Zebra Person');
    });

    test('people can be sorted by name descending', function (): void {
        $personZ = Person::factory()->create(['name' => 'Zebra Person']);
        $personA = Person::factory()->create(['name' => 'Alpha Person']);
        $personB = Person::factory()->create(['name' => 'Beta Person']);

        $this->event->people()->attach($personZ->id, ['role' => PersonRole::SPEAKER->value]);
        $this->event->people()->attach($personA->id, ['role' => PersonRole::SPEAKER->value]);
        $this->event->people()->attach($personB->id, ['role' => PersonRole::SPEAKER->value]);

        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.people.index', [
                'exhibition' => $this->exhibition,
                'sort' => '-name',
            ]));

        $response->assertOk();

        $people = $response->viewData('page')['props']['people']['data'];
        expect($people[0]['name'])->toBe('Zebra Person')
            ->and($people[1]['name'])->toBe('Beta Person')
            ->and($people[2]['name'])->toBe('Alpha Person');
    });
});

describe('Person Search', function (): void {
    beforeEach(function (): void {
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);

        $this->exhibition = Exhibition::factory()->create();
        $this->event = Event::factory()->for($this->exhibition)->create();
    });

    test('people can be searched by name', function (): void {
        $person1 = Person::factory()->create(['name' => 'John Smith']);
        $person2 = Person::factory()->create(['name' => 'Jane Doe']);
        $person3 = Person::factory()->create(['name' => 'John Anderson']);

        $this->event->people()->attach($person1->id, ['role' => PersonRole::SPEAKER->value]);
        $this->event->people()->attach($person2->id, ['role' => PersonRole::SPEAKER->value]);
        $this->event->people()->attach($person3->id, ['role' => PersonRole::SPEAKER->value]);

        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.people.index', [
                'exhibition' => $this->exhibition,
                'search' => 'John',
            ]));

        $response->assertOk();

        $people = $response->viewData('page')['props']['people']['data'];
        expect(count($people))->toBe(2)
            ->and($people[0]['name'])->toContain('John')
            ->and($people[1]['name'])->toContain('John');
    });

    test('search is case insensitive', function (): void {
        $person = Person::factory()->create(['name' => 'Alice Johnson']);

        $this->event->people()->attach($person->id, ['role' => PersonRole::SPEAKER->value]);

        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.people.index', [
                'exhibition' => $this->exhibition,
                'search' => 'alice',
            ]));

        $response->assertOk();

        $people = $response->viewData('page')['props']['people']['data'];
        expect(count($people))->toBe(1)
            ->and($people[0]['name'])->toBe('Alice Johnson');
    });
});

describe('Person Events Count', function (): void {
    it('includes events count for each person', function (): void {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(UserRole::SUPER_ADMIN);

        $exhibition = Exhibition::factory()->create();

        $event1 = Event::factory()->for($exhibition)->create();
        $event2 = Event::factory()->for($exhibition)->create();
        $event3 = Event::factory()->for($exhibition)->create();

        $person = Person::factory()->create();

        $event1->people()->attach($person->id, ['role' => PersonRole::SPEAKER->value]);
        $event2->people()->attach($person->id, ['role' => PersonRole::ORGANIZER->value]);
        $event3->people()->attach($person->id, ['role' => PersonRole::HOST->value]);

        $response = actingAs($superAdmin)
            ->get(route('admin.exhibitions.people.index', $exhibition));

        $response->assertOk()
            ->assertInertia(
                fn($page) => $page->component('admin/People/Index')
                    ->where('people.data.0.events_count', 3)
            );
    });
});

describe('Person Edit Page Access', function (): void {
    it('super admin can access edit page for any person', function (): void {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(UserRole::SUPER_ADMIN);

        $exhibition = Exhibition::factory()->create();
        $event = Event::factory()->for($exhibition)->create();
        $person = Person::factory()->create();

        $event->people()->attach($person->id, ['role' => PersonRole::SPEAKER->value]);

        actingAs($superAdmin)
            ->get(route('admin.exhibitions.people.edit', [$exhibition, $person]))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->component('admin/People/Edit')
                    ->has('exhibition')
                    ->has('person')
            );
    });

    it('admin can access edit page only for people in assigned exhibitions', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        $assignedExhibition = Exhibition::factory()->create();
        $assignedExhibition->users()->attach($admin);
        $assignedEvent = Event::factory()->for($assignedExhibition)->create();
        $assignedPerson = Person::factory()->create();
        $assignedEvent->people()->attach($assignedPerson->id, ['role' => PersonRole::SPEAKER->value]);

        $unassignedExhibition = Exhibition::factory()->create();
        $unassignedEvent = Event::factory()->for($unassignedExhibition)->create();
        $unassignedPerson = Person::factory()->create();
        $unassignedEvent->people()->attach($unassignedPerson->id, ['role' => PersonRole::SPEAKER->value]);

        actingAs($admin)
            ->get(route('admin.exhibitions.people.edit', [$assignedExhibition, $assignedPerson]))
            ->assertOk();

        actingAs($admin)
            ->get(route('admin.exhibitions.people.edit', [$unassignedExhibition, $unassignedPerson]))
            ->assertForbidden();
    });
});

describe('Person Deduplication', function (): void {
    it('shows each person only once even if they have multiple roles', function (): void {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(UserRole::SUPER_ADMIN);

        $exhibition = Exhibition::factory()->create();
        $event = Event::factory()->for($exhibition)->create();
        $person = Person::factory()->create(['name' => 'Multi Role Person']);

        // Attach same person with multiple roles
        $event->people()->attach($person->id, ['role' => PersonRole::SPEAKER->value]);
        $event->people()->attach($person->id, ['role' => PersonRole::ORGANIZER->value]);
        $event->people()->attach($person->id, ['role' => PersonRole::HOST->value]);

        $response = actingAs($superAdmin)
            ->get(route('admin.exhibitions.people.index', $exhibition));

        $response->assertOk();

        $people = $response->viewData('page')['props']['people']['data'];
        expect(count($people))->toBe(1)
            ->and($people[0]['name'])->toBe('Multi Role Person');
    });
});
