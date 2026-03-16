<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Exhibition;
use App\Models\Image;
use App\Models\InfoItem;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;

describe('Admin Info Item Permission Test', function (): void {
    beforeEach(function (): void {
        $this->exhibition = Exhibition::factory()->create();
        $this->infoItem = InfoItem::factory()->for($this->exhibition)->create();
        $this->route = "/admin/info-items";
        $this->payload = [
            'title' => 'new title',
            'url'   => 'https://example.com',
        ];
    });

    it('allows admins with access to this exhibition to enter this page', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);
        $this->exhibition->users()->attach($admin->id);

        $this->actingAs($admin)
            ->get($this->route)
            ->assertOk();
    });

    it('forbids admins without access to this exhibition from entering this page', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        $this->actingAs($admin)
            ->get($this->route)
            ->assertForbidden();
    });

    it('forbids admins without access from creating an info item', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        $this->actingAs($admin)
            ->post($this->route, $this->payload)
            ->assertForbidden();

        $this->assertDatabaseMissing('info_items', [
            'title' => $this->payload['title'],
        ]);
    });

    it('forbids admins without access from updating an info item', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        $this->actingAs($admin)
            ->put("{$this->route}/{$this->infoItem->id}", $this->payload)
            ->assertForbidden();

        $this->assertDatabaseMissing('info_items', [
            'title' => $this->payload['title'],
        ]);
    });

    it('forbids admins without access from deleting an info item', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        $this->actingAs($admin)
            ->delete("{$this->route}/{$this->infoItem->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('info_items', [
            'id' => $this->infoItem->id,
        ]);
    });

    it('forbids exponents from entering this page', function (): void {
        $exponent = User::factory()->create();
        $exponent->assignRole(UserRole::EXPONENT);

        $this->actingAs($exponent)
            ->get($this->route)
            ->assertForbidden();
    });

    it('forbids exponents from creating an info item', function (): void {
        $exponent = User::factory()->create();
        $exponent->assignRole(UserRole::EXPONENT);

        $this->actingAs($exponent)
            ->post($this->route, $this->payload)
            ->assertForbidden();

        $this->assertDatabaseMissing('info_items', [
            'title' => $this->payload['title'],
        ]);
    });

    it('forbids exponents from updating an info item', function (): void {
        $exponent = User::factory()->create();
        $exponent->assignRole(UserRole::EXPONENT);

        $this->actingAs($exponent)
            ->put("{$this->route}/{$this->infoItem->id}", $this->payload)
            ->assertForbidden();

        $this->assertDatabaseMissing('info_items', [
            'title' => $this->payload['title'],
        ]);
    });

    it('forbids exponents from deleting an info item', function (): void {
        $exponent = User::factory()->create();
        $exponent->assignRole(UserRole::EXPONENT);

        $this->actingAs($exponent)
            ->delete("{$this->route}/{$this->infoItem->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('info_items', [
            'id' => $this->infoItem->id,
        ]);
    });

    it('forbids users from entering this page', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        $this->actingAs($user)
            ->get($this->route)
            ->assertForbidden();
    });

    it('forbids users from creating an info item', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        $this->actingAs($user)
            ->post($this->route, $this->payload)
            ->assertForbidden();

        $this->assertDatabaseMissing('info_items', [
            'title' => $this->payload['title'],
        ]);
    });

    it('forbids users from updating an info item', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        $this->actingAs($user)
            ->put("{$this->route}/{$this->infoItem->id}", $this->payload)
            ->assertForbidden();

        $this->assertDatabaseMissing('info_items', [
            'title' => $this->payload['title'],
        ]);
    });

    it('forbids users from deleting an info item', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        $this->actingAs($user)
            ->delete("{$this->route}/{$this->infoItem->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('info_items', [
            'id' => $this->infoItem->id,
        ]);
    });

    it('forbids unregistered users to enter this page', function (): void {
        $this->get($this->route)
            ->assertRedirect('/login');
    });

    it('forbids unregistered users from creating an info item', function (): void {
        $this->post($this->route, $this->payload)
            ->assertRedirect('/login');

        $this->assertDatabaseMissing('info_items', [
            'title' => $this->payload['title'],
        ]);
    });

    it('forbids unregistered users from updating an info item', function (): void {
        $this->put("{$this->route}/{$this->infoItem->id}", $this->payload)
            ->assertRedirect('/login');

        $this->assertDatabaseMissing('info_items', [
            'title' => $this->payload['title'],
        ]);
    });

    it('forbids unregistered users from deleting an info item', function (): void {
        $this->delete("{$this->route}/{$this->infoItem->id}")
            ->assertRedirect('/login');

        $this->assertDatabaseHas('info_items', [
            'id' => $this->infoItem->id,
        ]);
    });
});


describe('Admin Info Item Test', function (): void {

    it('successfully creates an info item with an image', function (): void {
        Storage::fake('local');

        $user = User::factory()->create([
            'email'    => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $route = "/admin/info-items";

        $image = UploadedFile::fake()->image('photo.jpg');
        $payload = [
            'title' => 'new title',
            'url'   => 'https://example.com',
            'alt'   => 'alt for image',
            'image' => $image,
        ];

        $this->actingAs($user)
            ->post($route, $payload)
            ->assertRedirect($route);

        $infoItem = InfoItem::where('title', $payload['title'])->first();
        expect($infoItem)->not->toBeNull();

        $this->assertDatabaseHas('info_items', [
            'title' => $payload['title'],
            'url'   => $payload['url'],
        ]);

        expect($infoItem->image)->not->toBeNull();
    });

    it('successfully creates an info item without an image', function (): void {
        $user = User::factory()->create([
            'email'    => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $route = "/admin/info-items";

        $payload = [
            'title' => 'new title',
            'url'   => 'https://example.com',
        ];

        $this->actingAs($user)
            ->post($route, $payload)
            ->assertRedirect($route);

        $this->assertDatabaseHas('info_items', [
            'title' => $payload['title'],
            'url'   => $payload['url'],
        ]);

        $infoItem = InfoItem::where('title', $payload['title'])->first();
        expect($infoItem->image)->toBeNull();
    });

    it('successfully updates an info item', function (): void {
        $user = User::factory()->create([
            'email'    => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $infoItem = InfoItem::factory()->for($exhibition)->create();
        $route = "/admin/info-items";

        $this->actingAs($user)
            ->get("{$route}/{$infoItem->id}/edit")
            ->assertInertia(
                fn(AssertableInertia $page): AssertableInertia =>
                $page->component('admin/InfoItems/Edit')
            );

        $payload = [
            'title' => 'updated title',
            'url'   => 'https://updated-example.com',
        ];

        $this->actingAs($user)
            ->put("{$route}/{$infoItem->id}", $payload)
            ->assertRedirect($route);

        $this->assertDatabaseHas('info_items', [
            'id'    => $infoItem->id,
            'title' => $payload['title'],
            'url'   => $payload['url'],
        ]);
    });

    it('successfully updates an info item image', function (): void {
        Storage::fake('local');

        $user = User::factory()->create([
            'email'    => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $infoItem = InfoItem::factory()->for($exhibition)->create();
        $route = "/admin/info-items";

        $image = UploadedFile::fake()->image('new-photo.jpg');
        $payload = [
            'title' => 'updated title',
            'url'   => 'https://updated-example.com',
            'image' => $image,
        ];

        $this->actingAs($user)
            ->put("{$route}/{$infoItem->id}", $payload)
            ->assertRedirect($route);

        $infoItem->refresh();
        expect($infoItem->image)->not->toBeNull();
    });

    it('renders the info items index page', function (): void {
        $user = User::factory()->create([
            'email'    => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $infoItems = InfoItem::factory(3)->for($exhibition)->create();
        $route = "/admin/info-items";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route);
        $page->assertSee('Информация и материалы');
        $page->assertSee($infoItems[0]->title);
    });

    it('allows to create an info item with valid data', function (): void {
        $user = User::factory()->create([
            'email'    => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $route = "/admin/info-items";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee('Информация и материалы')
            ->click('@create-info-item')
            ->assertSee('Создать информационный элемент')
            ->fill('title', 'New Info Item')
            ->fill('url', 'https://example.com')
            ->submit()
            ->assertPathEndsWith($route);
    });

    it('doesnt allow to create an info item when the title is too long', function (): void {
        $user = User::factory()->create([
            'email'    => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $route = "/admin/info-items";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee('Информация и материалы')
            ->click('@create-info-item')
            ->assertSee('Создать информационный элемент')
            ->fill('title', generateTextWithChars(260))
            ->fill('url', 'https://example.com')
            ->submit()
            ->assertSee('Название не должно превышать 255 символов');
    });

    it('doesnt allow to create an info item when the url is invalid', function (): void {
        $user = User::factory()->create([
            'email'    => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $route = "/admin/info-items";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee('Информация и материалы')
            ->click('@create-info-item')
            ->assertSee('Создать информационный элемент')
            ->fill('title', 'Valid Title')
            ->fill('url', 'not-a-valid-url')
            ->submit()
            ->assertSee('Введите корректный URL');
    });

    it('allows to update an info item with valid data', function (): void {
        $user = User::factory()->create([
            'email'    => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $infoItem = InfoItem::factory()->for($exhibition)->create(['title' => 'Old Title']);
        $route = "/admin/info-items";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $newTitle = 'Updated Info Item Title';

        $page->navigate($route)
            ->assertSee('Информация и материалы')
            ->assertSee($infoItem->title)
            ->click('@edit-info-item-' . $infoItem->id)
            ->assertSee('Редактировать информационный элемент')
            ->clear('title')
            ->fill('title', $newTitle)
            ->clear('url')
            ->fill('url', 'https://updated-example.com')
            ->submit();

        $infoItem = $infoItem->fresh();
        $this->assertEquals($infoItem->title, $newTitle);
        $this->assertEquals($infoItem->url, 'https://updated-example.com');
    });

    it('doesnt allow to update an info item when the title is too long', function (): void {
        $user = User::factory()->create([
            'email'    => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $infoItem = InfoItem::factory()->for($exhibition)->create(['title' => 'Old Title']);
        $route = "/admin/info-items";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee('Информация и материалы')
            ->assertSee($infoItem->title)
            ->click('@edit-info-item-' . $infoItem->id)
            ->assertSee('Редактировать информационный элемент')
            ->clear('title')
            ->fill('title', generateTextWithChars(260))
            ->submit()
            ->assertSee('Название не должно превышать 255 символов');
    });

    it('doesnt allow to update an info item when the url is invalid', function (): void {
        $user = User::factory()->create([
            'email'    => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $infoItem = InfoItem::factory()->for($exhibition)->create(['title' => 'Old Title']);
        $route = "/admin/info-items";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee('Информация и материалы')
            ->assertSee($infoItem->title)
            ->click('@edit-info-item-' . $infoItem->id)
            ->assertSee('Редактировать информационный элемент')
            ->clear('url')
            ->fill('url', 'not-a-valid-url')
            ->submit()
            ->assertSee('Введите корректный URL');
    });

    it('allows to upload an image to the info item without an alt', function (): void {
        Storage::fake('local');

        $user = User::factory()->create([
            'email'    => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $infoItem = InfoItem::factory()->for($exhibition)->create();
        $route = "/admin/info-items";

        $image = UploadedFile::fake()->image('photo.jpg');
        $payload = [
            'title' => 'updated title',
            'url'   => 'https://updated-example.com',
            'image' => $image,
        ];

        $this->actingAs($user)
            ->put("{$route}/{$infoItem->id}", $payload);

        $infoItem->refresh();
        expect($infoItem->image)->not->toBeNull();
    });

    it('deletes an image file when the info item is deleted', function (): void {
        Storage::fake('public');

        $user = User::factory()->create([
            'email'    => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $infoItem = InfoItem::factory()->for($exhibition)->create();

        $image = UploadedFile::fake()->image('photo.jpg');
        $imagePath = Storage::disk('public')->put('info-items/images', $image);
        $infoItem->image()->create(Image::factory()->make(['webp' => $imagePath])->toArray());

        $route = "/admin/info-items";

        $this->actingAs($user)
            ->delete("{$route}/{$infoItem->id}")
            ->assertRedirect($route);

        $this->assertDatabaseMissing('info_items', ['id' => $infoItem->id]);
        $this->assertDatabaseMissing('images', ['imageable_id' => $infoItem->id]);
        Storage::assertMissing($imagePath);
    });

    it('allows to delete an info item', function (): void {
        $user = User::factory()->create([
            'email'    => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $infoItem = InfoItem::factory()->for($exhibition)->create(['title' => 'To Be Deleted']);
        $route = "/admin/info-items";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee('Информация и материалы')
            ->assertSee($infoItem->title)
            ->click('@edit-info-item-' . $infoItem->id)
            ->assertSee('Редактировать информационный элемент')
            ->click('@delete-info-item')
            ->click('@delete-btn')
            ->assertPathEndsWith($route)
            ->assertDontSee($infoItem->title);
    });

    it('displays the image on the edit page when an info item has an image', function (): void {
        Storage::fake('local');

        $user = User::factory()->create([
            'email'    => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $infoItem = InfoItem::factory()->for($exhibition)->create(['title' => 'With Image']);
        $infoItem->image()->create(Image::factory()->make()->toArray());

        $route = "/admin/info-items";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee('Информация и материалы')
            ->click('@edit-info-item-' . $infoItem->id)
            ->assertSee('Редактировать информационный элемент')
            ->assertPresent('@image-present');
    });
});
