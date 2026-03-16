<?php

declare(strict_types=1);

use App\Enums\PersonRole;
use App\Enums\UserRole;
use App\Models\Event;
use App\Models\Exhibition;
use App\Models\Person;
use App\Models\Stage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;

describe('Person Access Control', function (): void {
    beforeEach(function (): void {
        $this->exhibition = Exhibition::factory()->create();
    });

    it('redirects guest users to login', function (): void {
        get(route('admin.people.index'))
            ->assertRedirect(route('login'));
    });

    it('forbids USER role from accessing people page', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        actingAs($user)
            ->get(route('admin.people.index'))
            ->assertForbidden();
    });

    it('forbids EXPONENT role from accessing people page', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::EXPONENT);

        actingAs($user)
            ->get(route('admin.people.index'))
            ->assertForbidden();
    });
});

describe('Person Index', function (): void {
    beforeEach(function (): void {
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
        $this->event = Event::factory()->for($this->exhibition)->create();
    });

    it('sorts people by name ascending', function (): void {
        $personZ = Person::factory()->create(['name' => 'Zebra Person']);
        $personA = Person::factory()->create(['name' => 'Alpha Person']);
        $personB = Person::factory()->create(['name' => 'Beta Person']);

        $this->event->people()->attach($personZ->id, ['role' => PersonRole::SPEAKER->value]);
        $this->event->people()->attach($personA->id, ['role' => PersonRole::SPEAKER->value]);
        $this->event->people()->attach($personB->id, ['role' => PersonRole::SPEAKER->value]);

        $people = actingAs($this->superAdmin)
            ->get(route('admin.people.index', [
                'exhibition' => $this->exhibition,
                'sort'       => 'name',
            ]))
            ->assertOk()
            ->viewData('page')['props']['people']['data'];

        expect($people[0]['name'])->toBe('Alpha Person')
            ->and($people[1]['name'])->toBe('Beta Person')
            ->and($people[2]['name'])->toBe('Zebra Person');
    });

    it('sorts people by name descending', function (): void {
        $personZ = Person::factory()->create(['name' => 'Zebra Person']);
        $personA = Person::factory()->create(['name' => 'Alpha Person']);
        $personB = Person::factory()->create(['name' => 'Beta Person']);

        $this->event->people()->attach($personZ->id, ['role' => PersonRole::SPEAKER->value]);
        $this->event->people()->attach($personA->id, ['role' => PersonRole::SPEAKER->value]);
        $this->event->people()->attach($personB->id, ['role' => PersonRole::SPEAKER->value]);

        $people = actingAs($this->superAdmin)
            ->get(route('admin.people.index', [
                'exhibition' => $this->exhibition,
                'sort'       => '-name',
            ]))
            ->assertOk()
            ->viewData('page')['props']['people']['data'];

        expect($people[0]['name'])->toBe('Zebra Person')
            ->and($people[1]['name'])->toBe('Beta Person')
            ->and($people[2]['name'])->toBe('Alpha Person');
    });

    it('searches people by name', function (): void {
        $person1 = Person::factory()->create(['name' => 'John Smith']);
        $person2 = Person::factory()->create(['name' => 'Jane Doe']);
        $person3 = Person::factory()->create(['name' => 'John Anderson']);

        $this->event->people()->attach($person1->id, ['role' => PersonRole::SPEAKER->value]);
        $this->event->people()->attach($person2->id, ['role' => PersonRole::SPEAKER->value]);
        $this->event->people()->attach($person3->id, ['role' => PersonRole::SPEAKER->value]);

        $people = actingAs($this->superAdmin)
            ->get(route('admin.people.index', [
                'exhibition' => $this->exhibition,
                'search'     => 'John',
            ]))
            ->assertOk()
            ->viewData('page')['props']['people']['data'];

        expect(count($people))->toBe(2)
            ->and($people[0]['name'])->toContain('John')
            ->and($people[1]['name'])->toContain('John');
    });

    it('search is case insensitive', function (): void {
        $person = Person::factory()->create(['name' => 'Alice Johnson']);
        $this->event->people()->attach($person->id, ['role' => PersonRole::SPEAKER->value]);

        $people = actingAs($this->superAdmin)
            ->get(route('admin.people.index', [
                'exhibition' => $this->exhibition,
                'search'     => 'alice',
            ]))
            ->assertOk()
            ->viewData('page')['props']['people']['data'];

        expect(count($people))->toBe(1)
            ->and($people[0]['name'])->toBe('Alice Johnson');
    });

    it('includes events count for each person', function (): void {
        $event1 = Event::factory()->for($this->exhibition)->create();
        $event2 = Event::factory()->for($this->exhibition)->create();
        $event3 = Event::factory()->for($this->exhibition)->create();

        $person = Person::factory()->create();
        $event1->people()->attach($person->id, ['role' => PersonRole::SPEAKER->value]);
        $event2->people()->attach($person->id, ['role' => PersonRole::ORGANIZER->value]);
        $event3->people()->attach($person->id, ['role' => PersonRole::HOST->value]);

        actingAs($this->superAdmin)
            ->get(route('admin.people.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->component('admin/People/Index')
                    ->where('people.data.0.events_count', 3)
            );
    });

    it('shows each person only once even if they have multiple roles', function (): void {
        $person = Person::factory()->create(['name' => 'Multi Role Person']);

        $this->event->people()->attach($person->id, ['role' => PersonRole::SPEAKER->value]);
        $this->event->people()->attach($person->id, ['role' => PersonRole::ORGANIZER->value]);
        $this->event->people()->attach($person->id, ['role' => PersonRole::HOST->value]);

        $people = actingAs($this->superAdmin)
            ->get(route('admin.people.index'))
            ->assertOk()
            ->viewData('page')['props']['people']['data'];

        expect(count($people))->toBe(1)
            ->and($people[0]['name'])->toBe('Multi Role Person');
    });

    it('handles exhibition with no people', function (): void {
        actingAs($this->superAdmin)
            ->get(route('admin.people.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('people.data', fn($people): bool => count($people) === 0)
            );
    });
});

describe('Person Create', function (): void {
    beforeEach(function (): void {
        Storage::fake('public');
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
    });

    it('displays create form', function (): void {
        actingAs($this->superAdmin)
            ->get(route('admin.people.create'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->component('admin/People/Create')
                    ->has('exhibition')
            );
    });

    it('creates person with basic data', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.people.store'), [
                'name'    => 'Test Person',
                'regalia' => 'PhD in Computer Science',
                'bio'     => 'This is a test bio for the person',
                'telegram' => '@testuser',
            ])
            ->assertRedirect(route('admin.people.index'));

        assertDatabaseHas('people', [
            'name'     => 'Test Person',
            'regalia'  => 'PhD in Computer Science',
            'bio'      => 'This is a test bio for the person',
            'telegram' => '@testuser',
        ]);
    });

    it('creates person with avatar', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.people.store'), [
                'name'    => 'Test Person',
                'regalia' => 'PhD in Computer Science',
                'bio'     => 'This is a test bio for the person',
                'avatar'  => UploadedFile::fake()->image('photo.jpg'),
            ]);

        $person = Person::query()->where('name', 'Test Person')->first();
        expect($person->avatar)->not->toBeNull()
            ->and($person->avatar->alt)->toBe($person->name);
    });

    it('creates person with logo', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.people.store'), [
                'name'    => 'Test Person',
                'regalia' => 'PhD in Computer Science',
                'bio'     => 'This is a test bio for the person',
                'logo'    => UploadedFile::fake()->image('logo.png'),
            ]);

        $person = Person::query()->where('name', 'Test Person')->first();
        expect($person->logo)->not->toBeNull()
            ->and($person->logo->alt)->toBe($person->name);
    });

    it('creates person with both avatar and logo', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.people.store'), [
                'name'    => 'Test Person',
                'regalia' => 'PhD in Computer Science',
                'bio'     => 'This is a test bio for the person',
                'avatar'  => UploadedFile::fake()->image('avatar.jpg'),
                'logo'    => UploadedFile::fake()->image('logo.png'),
            ]);

        $person = Person::query()->where('name', 'Test Person')->first();
        expect($person->avatar)->not->toBeNull()
            ->and($person->logo)->not->toBeNull()
            ->and($person->avatar->alt)->toBe($person->name)
            ->and($person->logo->alt)->toBe($person->name);
    });

    it('creates person without optional fields', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.people.store'), [
                'name'    => 'Minimal Person',
                'regalia' => 'Basic regalia text here',
                'bio'     => 'Basic bio text here',
            ])
            ->assertRedirect();

        assertDatabaseHas('people', ['name' => 'Minimal Person', 'telegram' => null]);
    });

    it('handles very long name at boundary', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.people.store'), [
                'name'    => str_repeat('a', 255),
                'regalia' => 'Valid regalia here',
                'bio'     => 'Valid bio here',
            ])
            ->assertRedirect();

        assertDatabaseHas('people', ['name' => str_repeat('a', 255)]);
    });

    it('validates required fields', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.people.store'), [])
            ->assertSessionHasErrors(['name', 'regalia', 'bio']);
    });

    it('validates regalia length', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.people.store'), [
                'name'    => 'Test Person',
                'regalia' => 'Short',
                'bio'     => 'Valid bio text here',
            ])
            ->assertSessionHasErrors('regalia');
    });

    it('validates bio length', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.people.store'), [
                'name'    => 'Test Person',
                'regalia' => 'Valid regalia text',
                'bio'     => 'Short',
            ])
            ->assertSessionHasErrors('bio');
    });

    it('validates avatar is image', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.people.store'), [
                'name'    => 'Test Person',
                'regalia' => 'Valid regalia text',
                'bio'     => 'Valid bio text here',
                'avatar'  => UploadedFile::fake()->create('document.pdf'),
            ])
            ->assertSessionHasErrors('avatar');
    });

    it('validates avatar size', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.people.store'), [
                'name'    => 'Test Person',
                'regalia' => 'Valid regalia text',
                'bio'     => 'Valid bio text here',
                'avatar'  => UploadedFile::fake()->create('avatar.jpg', 60000),
            ])
            ->assertSessionHasErrors('avatar');
    });
});

describe('Person Edit', function (): void {
    beforeEach(function (): void {
        Storage::fake('public');
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
        $this->person = Person::factory()->create(['name' => 'Original Name']);
    });

    it('displays edit form', function (): void {
        actingAs($this->superAdmin)
            ->get(route('admin.people.edit', [$this->person]))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->component('admin/People/Edit')
                    ->has('exhibition')
                    ->has('person')
            );
    });

    it('loads avatar and logo relationships', function (): void {
        $this->person->images()->create([
            'webp3x' => 'test3x.webp',
            'webp2x' => 'test2x.webp',
            'webp' => 'test.webp',
            'avif3x' => 'test3x.avif',
            'avif2x' => 'test2x.avif',
            'avif' => 'test.avif',
            'tiny' => 'test-tiny.webp',
            'alt' => 'Avatar alt',
            'type' => 'avatar',
        ]);
        $this->person->images()->create([
            'webp3x' => 'logo3x.webp',
            'webp2x' => 'logo2x.webp',
            'webp' => 'logo.webp',
            'avif3x' => 'logo3x.avif',
            'avif2x' => 'logo2x.avif',
            'avif' => 'logo.avif',
            'tiny' => 'logo-tiny.webp',
            'alt' => 'Logo alt',
            'type' => 'logo',
        ]);

        actingAs($this->superAdmin)
            ->get(route('admin.people.edit', [$this->person]))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('person.avatar.alt', 'Avatar alt')
                    ->where('person.logo.alt', 'Logo alt')
            );
    });

    it('handles person without avatar', function (): void {
        actingAs($this->superAdmin)
            ->get(route('admin.people.edit', [$this->person]))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->where('person.avatar', null)
            );
    });

    it('handles person without logo', function (): void {
        actingAs($this->superAdmin)
            ->get(route('admin.people.edit', [$this->person]))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->where('person.logo', null)
            );
    });

    it('updates basic person data', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.people.update', [$this->person]), [
                '_method'  => 'PUT',
                'name'     => 'Updated Name',
                'regalia'  => 'Updated regalia text here',
                'bio'      => 'Updated bio text here',
                'telegram' => '@updateduser',
            ])
            ->assertRedirect();

        assertDatabaseHas('people', [
            'id'       => $this->person->id,
            'name'     => 'Updated Name',
            'telegram' => '@updateduser',
        ]);
    });

    it('updates person avatar', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.people.update', [$this->person]), [
                '_method' => 'PUT',
                'name'    => $this->person->name,
                'regalia' => $this->person->regalia,
                'bio'     => $this->person->bio,
                'avatar'  => UploadedFile::fake()->image('new-photo.jpg'),
            ]);

        $this->person->refresh();
        expect($this->person->avatar)->not->toBeNull();
    });

    it('replaces existing avatar', function (): void {
        $oldAvatar = $this->person->avatar()->create([
            'webp3x' => 'old3x.webp',
            'webp2x' => 'old2x.webp',
            'webp' => 'old.webp',
            'avif3x' => 'old3x.avif',
            'avif2x' => 'old2x.avif',
            'avif' => 'old.avif',
            'tiny' => 'old-tiny.webp',
            'alt' => 'alt',
            'type' => 'avatar',
        ]);

        actingAs($this->superAdmin)
            ->post(route('admin.people.update', [$this->person]), [
                '_method' => 'PUT',
                'name'    => $this->person->name,
                'regalia' => $this->person->regalia,
                'bio'     => $this->person->bio,
                'avatar'  => UploadedFile::fake()->image('new-photo.jpg'),
            ]);

        $this->person->refresh();
        expect($this->person->avatar->webp)->not->toBe($oldAvatar->webp);
    });

    it('validates update data', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.people.update', [$this->person]), [
                '_method' => 'PUT',
                'name'    => '',
            ])
            ->assertSessionHasErrors('name');
    });
});

describe('Person Destroy', function (): void {
    beforeEach(function (): void {
        Storage::fake('public');
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
        $this->person = Person::factory()->create();
    });

    it('deletes person', function (): void {
        actingAs($this->superAdmin)
            ->delete(route('admin.people.destroy', [$this->person]))
            ->assertRedirect();

        assertDatabaseMissing('people', ['id' => $this->person->id]);
    });

    it('cascades delete to event_person pivot', function (): void {
        $stage = Stage::factory()->create();
        $event = Event::factory()->for($this->exhibition)->for($stage)->create();
        $event->people()->attach($this->person->id, ['role' => 1]);

        actingAs($this->superAdmin)
            ->delete(route('admin.people.destroy', [$this->person]));

        assertDatabaseMissing('event_person', ['person_id' => $this->person->id]);
    });

    it('deletes associated images from database', function (): void {
        $avatar = $this->person->images()->create([
            'webp3x' => 'avatar3x.webp',
            'webp2x' => 'avatar2x.webp',
            'webp' => 'avatar.webp',
            'avif3x' => 'avatar3x.avif',
            'avif2x' => 'avatar2x.avif',
            'avif' => 'avatar.avif',
            'tiny' => 'avatar-tiny.webp',
            'alt' => 'Avatar',
            'type' => 'avatar',
        ]);

        actingAs($this->superAdmin)
            ->delete(route('admin.people.destroy', [$this->person]));

        assertDatabaseMissing('people', ['id' => $this->person->id]);
        assertDatabaseMissing('images', ['id' => $avatar->id]);
    });

    it('does not delete associated events', function (): void {
        $stage = Stage::factory()->create();
        $event = Event::factory()->for($this->exhibition)->for($stage)->create();
        $event->people()->attach($this->person->id, ['role' => 1]);

        actingAs($this->superAdmin)
            ->delete(route('admin.people.destroy', [$this->person]));

        assertDatabaseHas('events', ['id' => $event->id]);
    });

    it('admin can only delete people from assigned exhibitions', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        $stage = Stage::factory()->create();
        $assignedExhibition = Exhibition::factory()->create();
        $assignedExhibition->users()->attach($admin);
        $event = Event::factory()->for($assignedExhibition)->for($stage)->create();

        $person1 = Person::factory()->create();
        $person2 = Person::factory()->create();
        $event->people()->attach($person1, ['role' => 1]);

        actingAs($admin)
            ->delete(route('admin.people.destroy', [$assignedExhibition, $person1]))
            ->assertRedirect();

        actingAs($admin)
            ->delete(route('admin.people.destroy', [$assignedExhibition, $person2]))
            ->assertForbidden();

        assertDatabaseMissing('people', ['id' => $person1->id]);
        assertDatabaseHas('people', ['id' => $person2->id]);
    });
});
