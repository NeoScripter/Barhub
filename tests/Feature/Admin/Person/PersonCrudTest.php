<?php

declare(strict_types=1);

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

describe('Person Create', function (): void {
    beforeEach(function (): void {
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
    });

    test('displays create form', function (): void {
        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.people.create', $this->exhibition));

        $response->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->component('admin/People/Create')
                    ->has('exhibition')
            );
    });
});

describe('Person Store', function (): void {
    beforeEach(function (): void {
        Storage::fake('public');
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
    });

    test('creates person with basic data', function (): void {
        $data = [
            'name' => 'Test Person',
            'regalia' => 'PhD in Computer Science',
            'bio' => 'This is a test bio for the person',
            'telegram' => '@testuser',
        ];

        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.people.store', $this->exhibition), $data)
            ->assertRedirect(route('admin.exhibitions.people.index', $this->exhibition));

        assertDatabaseHas('people', [
            'name' => 'Test Person',
            'regalia' => 'PhD in Computer Science',
            'bio' => 'This is a test bio for the person',
            'telegram' => '@testuser',
        ]);
    });

    test('creates person with avatar', function (): void {
        // Use create() with mime type instead of image()
        $avatar = UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg');

        $data = [
            'name' => 'Test Person',
            'regalia' => 'PhD in Computer Science',
            'bio' => 'This is a test bio for the person',
            'avatar' => $avatar,
            'avatar_alt' => 'Avatar of Test Person',
        ];

        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.people.store', $this->exhibition), $data);

        $person = Person::where('name', 'Test Person')->first();
        $person->load('avatar');

        expect($person->avatar)->not->toBeNull();
        expect($person->avatar->alt)->toBe('Avatar of Test Person');
    })->skip();

    test('creates person with logo', function (): void {
        $logo = UploadedFile::fake()->create('logo.png', 100, 'image/png');

        $data = [
            'name' => 'Test Person',
            'regalia' => 'PhD in Computer Science',
            'bio' => 'This is a test bio for the person',
            'logo' => $logo,
            'logo_alt' => 'Logo of Test Person',
        ];

        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.people.store', $this->exhibition), $data);

        $person = Person::where('name', 'Test Person')->first();

        expect($person?->logo)->not->toBeNull();
        expect($person?->logo?->alt)->toBe('Logo of Test Person');
    })->skip();

    test('creates person with both avatar and logo', function (): void {
        $avatar = UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg');
        $logo = UploadedFile::fake()->create('logo.png', 100, 'image/png');

        $data = [
            'name' => 'Test Person',
            'regalia' => 'PhD in Computer Science',
            'bio' => 'This is a test bio for the person',
            'avatar' => $avatar,
            'avatar_alt' => 'Avatar alt text',
            'logo' => $logo,
            'logo_alt' => 'Logo alt text',
        ];

        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.people.store', $this->exhibition), $data);

        $person = Person::where('name', 'Test Person')->first();

        expect($person?->avatar)->not->toBeNull();
        expect($person?->logo)->not->toBeNull();
    })->skip();

    test('creates person without optional fields', function (): void {
        $data = [
            'name' => 'Minimal Person',
            'regalia' => 'Basic regalia text here',
            'bio' => 'Basic bio text here',
        ];

        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.people.store', $this->exhibition), $data)
            ->assertRedirect();

        assertDatabaseHas('people', [
            'name' => 'Minimal Person',
            'telegram' => null,
        ]);
    });

    test('validates required fields', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.people.store', $this->exhibition), [])
            ->assertSessionHasErrors(['name', 'regalia', 'bio']);
    });

    test('validates regalia length', function (): void {
        $data = [
            'name' => 'Test Person',
            'regalia' => 'Short',
            'bio' => 'Valid bio text here',
        ];

        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.people.store', $this->exhibition), $data)
            ->assertSessionHasErrors('regalia');
    });

    test('validates bio length', function (): void {
        $data = [
            'name' => 'Test Person',
            'regalia' => 'Valid regalia text',
            'bio' => 'Short',
        ];

        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.people.store', $this->exhibition), $data)
            ->assertSessionHasErrors('bio');
    });

    test('validates avatar is image', function (): void {
        $file = UploadedFile::fake()->create('document.pdf');

        $data = [
            'name' => 'Test Person',
            'regalia' => 'Valid regalia text',
            'bio' => 'Valid bio text here',
            'avatar' => $file,
        ];

        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.people.store', $this->exhibition), $data)
            ->assertSessionHasErrors('avatar');
    });

    test('validates avatar size', function (): void {
        $file = UploadedFile::fake()->create('avatar.jpg', 11000); // 11MB

        $data = [
            'name' => 'Test Person',
            'regalia' => 'Valid regalia text',
            'bio' => 'Valid bio text here',
            'avatar' => $file,
        ];

        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.people.store', $this->exhibition), $data)
            ->assertSessionHasErrors('avatar');
    });
});

describe('Person Edit', function (): void {
    beforeEach(function (): void {
        Storage::fake('public');
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
        $this->person = Person::factory()->create();
    });

    test('displays edit form', function (): void {
        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.people.edit', [$this->exhibition, $this->person]));

        $response->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->component('admin/People/Edit')
                    ->has('exhibition')
                    ->has('person')
            );
    });

    test('loads avatar and logo relationships', function (): void {
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

        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.people.edit', [$this->exhibition, $this->person]));

        $response->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('person.avatar.alt', 'Avatar alt')
                    ->where('person.logo.alt', 'Logo alt')
            );
    });
});

describe('Person Update', function (): void {
    beforeEach(function (): void {
        Storage::fake('public');
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
        $this->person = Person::factory()->create([
            'name' => 'Original Name',
        ]);
    });

    test('updates basic person data', function (): void {
        $data = [
            'name' => 'Updated Name',
            'regalia' => 'Updated regalia text here',
            'bio' => 'Updated bio text here',
            'telegram' => '@updateduser',
        ];

        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.people.update', [$this->exhibition, $this->person]), array_merge($data, ['_method' => 'PUT']))
            ->assertRedirect();

        assertDatabaseHas('people', [
            'id' => $this->person->id,
            'name' => 'Updated Name',
            'telegram' => '@updateduser',
        ]);
    });

    test('updates person avatar', function (): void {
        $newAvatar = UploadedFile::fake()->create('new-avatar.jpg', 100, 'image/jpeg');

        $data = [
            'name' => $this->person->name,
            'regalia' => $this->person->regalia,
            'bio' => $this->person->bio,
            'avatar' => $newAvatar,
            'avatar_alt' => 'New avatar alt',
            '_method' => 'PUT',
        ];

        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.people.update', [$this->exhibition, $this->person]), $data);

        $this->person->refresh();

        expect($this->person->avatar)->not->toBeNull();
        expect($this->person->avatar->alt)->toBe('New avatar alt');
    })->skip();

    test('replaces existing avatar', function (): void {
        // Create initial avatar
        $oldAvatar = $this->person->avatar()->create([
            'webp3x' => 'old3x.webp',
            'webp2x' => 'old2x.webp',
            'webp' => 'old.webp',
            'avif3x' => 'old3x.avif',
            'avif2x' => 'old2x.avif',
            'avif' => 'old.avif',
            'tiny' => 'old-tiny.webp',
            'alt' => 'Old alt',
            'type' => 'avatar',
        ]);

        $oldPath = $oldAvatar->webp;

        $newAvatar = UploadedFile::fake()->create('new-avatar.jpg', 100, 'image/jpeg');

        $data = [
            'name' => $this->person->name,
            'regalia' => $this->person->regalia,
            'bio' => $this->person->bio,
            'avatar' => $newAvatar,
            'avatar_alt' => 'New alt',
            '_method' => 'PUT',
        ];

        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.people.update', [$this->exhibition, $this->person]), $data);

        $this->person->refresh();

        expect($this->person->avatar->webp)->not->toBe($oldPath);
    })->skip();

    test('updates only avatar alt text', function (): void {
        $this->person->images()->create([
            'webp3x' => 'test3x.webp',
            'webp2x' => 'test2x.webp',
            'webp' => 'test.webp',
            'avif3x' => 'test3x.avif',
            'avif2x' => 'test2x.avif',
            'avif' => 'test.avif',
            'tiny' => 'test-tiny.webp',
            'alt' => 'Old alt',
            'type' => 'avatar',
        ]);

        $data = [
            'name' => $this->person->name,
            'regalia' => $this->person->regalia,
            'bio' => $this->person->bio,
            'avatar_alt' => 'New alt text only',
            '_method' => 'PUT',
        ];

        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.people.update', [$this->exhibition, $this->person]), $data);

        $this->person->refresh();

        expect($this->person->avatar->alt)->toBe('New alt text only');
        expect($this->person->avatar->webp)->toBe('test.webp'); // Image not changed
    });

    test('validates update data', function (): void {
        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.people.update', [$this->exhibition, $this->person]), [
                'name' => '',
                '_method' => 'PUT',
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

    test('deletes person', function (): void {
        actingAs($this->superAdmin)
            ->delete(route('admin.exhibitions.people.destroy', [$this->exhibition, $this->person]))
            ->assertRedirect();

        assertDatabaseMissing('people', [
            'id' => $this->person->id,
        ]);
    });

    test('cascades delete to event_person pivot', function (): void {
        $stage = Stage::factory()->create();
        $event = Event::factory()->for($this->exhibition)->for($stage)->create();
        $event->people()->attach($this->person->id, ['role' => 1]);

        actingAs($this->superAdmin)
            ->delete(route('admin.exhibitions.people.destroy', [$this->exhibition, $this->person]));

        assertDatabaseMissing('event_person', [
            'person_id' => $this->person->id,
        ]);
    });

    test('deletes associated images from database', function (): void {
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

        $avatarId = $avatar->id;

        actingAs($this->superAdmin)
            ->delete(route('admin.exhibitions.people.destroy', [$this->exhibition, $this->person]));

        // Check that person is deleted
        assertDatabaseMissing('people', [
            'id' => $this->person->id,
        ]);

        // Check that image is also deleted (due to cascade or model events)
        assertDatabaseMissing('images', [
            'id' => $avatarId,
        ]);
    });

    test('does not delete associated events', function (): void {
        $stage = Stage::factory()->create();
        $event = Event::factory()->for($this->exhibition)->for($stage)->create();
        $event->people()->attach($this->person->id, ['role' => 1]);

        actingAs($this->superAdmin)
            ->delete(route('admin.exhibitions.people.destroy', [$this->exhibition, $this->person]));

        assertDatabaseHas('events', [
            'id' => $event->id,
        ]);
    });

    test('admin can only delete people from assigned exhibitions', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        $assignedExhibition = Exhibition::factory()->create();
        $assignedExhibition->users()->attach($admin);

        $unassignedExhibition = Exhibition::factory()->create();

        $person1 = Person::factory()->create();
        $person2 = Person::factory()->create();

        actingAs($admin)
            ->delete(route('admin.exhibitions.people.destroy', [$assignedExhibition, $person1]))
            ->assertRedirect();

        actingAs($admin)
            ->delete(route('admin.exhibitions.people.destroy', [$unassignedExhibition, $person2]))
            ->assertForbidden();

        assertDatabaseMissing('people', ['id' => $person1->id]);
        assertDatabaseHas('people', ['id' => $person2->id]);
    });
});

describe('Person Edge Cases', function (): void {
    beforeEach(function (): void {
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
    });

    test('handles person without avatar', function (): void {
        $person = Person::factory()->create();

        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.people.edit', [$this->exhibition, $person]));

        $response->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('person.avatar', null)
            );
    });

    test('handles person without logo', function (): void {
        $person = Person::factory()->create();

        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.people.edit', [$this->exhibition, $person]));

        $response->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('person.logo', null)
            );
    });

    test('handles exhibition with no people', function (): void {
        $response = actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.people.index', $this->exhibition));

        $response->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->where('people.data', fn($people) => count($people) === 0)
            );
    });

    test('handles very long name at boundary', function (): void {
        $data = [
            'name' => str_repeat('a', 255),
            'regalia' => 'Valid regalia here',
            'bio' => 'Valid bio here',
        ];

        actingAs($this->superAdmin)
            ->post(route('admin.exhibitions.people.store', $this->exhibition), $data)
            ->assertRedirect();

        assertDatabaseHas('people', [
            'name' => str_repeat('a', 255),
        ]);
    });
});
