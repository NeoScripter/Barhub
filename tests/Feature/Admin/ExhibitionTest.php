<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Exhibition;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

describe('Exhibition Page Permissions', function (): void {
    beforeEach(function (): void {
        $this->startDate = now()->format('Y') . '-01-01';
        $this->endDate = now()->format('Y') . '-10-10';
    });

    it('super admin can see all exhibitions', function (): void {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(UserRole::SUPER_ADMIN);

        $exhibitions = Exhibition::factory(10)->create();

        $response = actingAs($superAdmin)
            ->get(route('admin.exhibitions.index'));

        $response
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->component('admin/Exhibitions/Index')
            );

        $exhibitions->each(
            fn($exhibition) => $response->assertSee($exhibition->name)
        );
    });

    it('forbids ADMIN role from accessing exhibitions page', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::ADMIN);

        actingAs($user)
            ->get(route('admin.exhibitions.index'))
            ->assertForbidden();
    });

    it('forbids USER role from accessing exhibitions page', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        actingAs($user)
            ->get(route('admin.exhibitions.index'))
            ->assertForbidden();
    });

    it('forbids EXPONENT role from accessing exhibitions page', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::EXPONENT);

        actingAs($user)
            ->get(route('admin.exhibitions.index'))
            ->assertForbidden();
    });

    it('redirects unauthenticated users to login page', function (): void {
        get(route('admin.exhibitions.index'))
            ->assertRedirect(route('login'));
    });

    it('forbids guest users from accessing exhibitions page', function (): void {
        get(route('admin.exhibitions.index'))
            ->assertRedirect(route('login'));
    });

    it('renders exhibition create page', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);

        $route = route('admin.exhibitions.create');

        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route);
        $page->assertSee("Создать выставку");
    });

    it('renders exhibition index page', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();

        $route = route('admin.exhibitions.index');

        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route);
        $page->assertSee($exhibition->name);
    });

    it('renders exhibition edit page', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();

        $route = route('admin.exhibitions.edit', ['exhibition' => $exhibition]);

        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route);
        $page->assertSee("Редактировать выставку");
    });

    it('forbids ADMIN role from creating an exhibition', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::ADMIN);

        actingAs($user)
            ->post(route('admin.exhibitions.store'), [
                'name'               => 'New Exhibition',
                'starts_at'          => $this->startDate,
                'ends_at'            => $this->endDate,
                'location'           => 'Moscow',
                'buildin_folder_url' => 'https://example.com',
                'is_active'          => true,
            ])
            ->assertForbidden();
    });

    it('forbids ADMIN role from updating an exhibition', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::ADMIN);
        $exhibition = Exhibition::factory()->create();

        actingAs($user)
            ->put(route('admin.exhibitions.update', $exhibition), [
                'name' => 'Updated Name',
            ])
            ->assertForbidden();
    });

    it('forbids ADMIN role from deleting an exhibition', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::ADMIN);
        $exhibition = Exhibition::factory()->create();

        actingAs($user)
            ->delete(route('admin.exhibitions.destroy', $exhibition))
            ->assertForbidden();

        $this->assertDatabaseHas('exhibitions', ['id' => $exhibition->id]);
    });

    it('forbids EXPONENT role from creating an exhibition', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::EXPONENT);

        actingAs($user)
            ->post(route('admin.exhibitions.store'), [
                'name'               => 'New Exhibition',
                'starts_at'          => $this->startDate,
                'ends_at'            => $this->endDate,
                'location'           => 'Moscow',
                'buildin_folder_url' => 'https://example.com',
                'is_active'          => true,
            ])
            ->assertForbidden();
    });

    it('forbids EXPONENT role from updating an exhibition', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::EXPONENT);
        $exhibition = Exhibition::factory()->create();

        actingAs($user)
            ->put(route('admin.exhibitions.update', $exhibition), [
                'name' => 'Updated Name',
            ])
            ->assertForbidden();
    });

    it('forbids EXPONENT role from deleting an exhibition', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::EXPONENT);
        $exhibition = Exhibition::factory()->create();

        actingAs($user)
            ->delete(route('admin.exhibitions.destroy', $exhibition))
            ->assertForbidden();

        $this->assertDatabaseHas('exhibitions', ['id' => $exhibition->id]);
    });

    it('forbids USER role from creating an exhibition', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        actingAs($user)
            ->post(route('admin.exhibitions.store'), [
                'name'               => 'New Exhibition',
                'starts_at'          => $this->startDate,
                'ends_at'            => $this->endDate,
                'location'           => 'Moscow',
                'buildin_folder_url' => 'https://example.com',
                'is_active'          => true,
            ])
            ->assertForbidden();
    });

    it('forbids USER role from updating an exhibition', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);
        $exhibition = Exhibition::factory()->create();

        actingAs($user)
            ->put(route('admin.exhibitions.update', $exhibition), [
                'name' => 'Updated Name',
            ])
            ->assertForbidden();
    });

    it('forbids USER role from deleting an exhibition', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);
        $exhibition = Exhibition::factory()->create();

        actingAs($user)
            ->delete(route('admin.exhibitions.destroy', $exhibition))
            ->assertForbidden();

        $this->assertDatabaseHas('exhibitions', ['id' => $exhibition->id]);
    });

    it('forbids guest users from creating an exhibition', function (): void {
        $this->post(route('admin.exhibitions.store'), [
            'name'               => 'New Exhibition',
            'starts_at'          => $this->startDate,
            'ends_at'            => $this->endDate,
            'location'           => 'Moscow',
            'buildin_folder_url' => 'https://example.com',
            'is_active'          => true,
        ])
            ->assertRedirect(route('login'));
    });

    it('forbids guest users from updating an exhibition', function (): void {
        $exhibition = Exhibition::factory()->create();

        $this->put(route('admin.exhibitions.update', $exhibition), [
            'name' => 'Updated Name',
        ])
            ->assertRedirect(route('login'));
    });

    it('forbids guest users from deleting an exhibition', function (): void {
        $exhibition = Exhibition::factory()->create();

        $this->delete(route('admin.exhibitions.destroy', $exhibition))
            ->assertRedirect(route('login'));

        $this->assertDatabaseHas('exhibitions', ['id' => $exhibition->id]);
    });
});

describe('Exhibition Browser Test', function (): void {
    beforeEach(function (): void {
        $this->startDate = now()->format('Y') . '-01-01';
        $this->endDate = now()->format('Y') . '-10-10';
    });

    it('allows to create an exhibition with valid data', function (): void {
        $user = User::factory()->create([
            'email'    => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);

        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate(route('admin.exhibitions.create'))
            ->assertSee('Создать выставку')
            ->fill('name', 'New Exhibition')
            ->fill('starts_at', $this->startDate)
            ->fill('ends_at', $this->endDate)
            ->fill('location', 'Moscow')
            ->fill('buildin_folder_url', 'https://example.com/folder')
            ->submit()
            ->assertPathEndsWith(route('admin.exhibitions.index', absolute: false));
    });

    it('doesnt allow to create an exhibition when the name is too long', function (): void {
        $user = User::factory()->create([
            'email'    => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);

        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate(route('admin.exhibitions.create'))
            ->assertSee('Создать выставку')
            ->fill('name', generateTextWithChars(260))
            ->fill('starts_at', $this->startDate)
            ->fill('ends_at', $this->endDate)
            ->fill('location', 'Moscow')
            ->fill('buildin_folder_url', 'https://example.com/folder')
            ->submit()
            ->assertSee('Название не должно превышать 255 символов');
    });

    it('doesnt allow to create an exhibition when the buildin_folder_url is invalid', function (): void {
        $user = User::factory()->create([
            'email'    => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);

        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate(route('admin.exhibitions.create'))
            ->assertSee('Создать выставку')
            ->fill('name', 'Valid Name')
            ->fill('starts_at', $this->startDate)
            ->fill('ends_at', $this->endDate)
            ->fill('location', 'Moscow')
            ->fill('buildin_folder_url', 'not-a-valid-url')
            ->submit()
            ->assertSee('Введите корректный URL папки');
    });

    it('doesnt allow to create an exhibition when ends_at is before starts_at', function (): void {
        $user = User::factory()->create([
            'email'    => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);

        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate(route('admin.exhibitions.create'))
            ->assertSee('Создать выставку')
            ->fill('name', 'Valid Name')
            ->fill('starts_at', $this->endDate)
            ->fill('ends_at', $this->startDate)
            ->fill('location', 'Moscow')
            ->fill('buildin_folder_url', 'https://example.com/folder')
            ->submit()
            ->assertSee('Дата окончания должна быть позже даты начала');
    });

    it('allows to update an exhibition with valid data', function (): void {
        $user = User::factory()->create([
            'email'    => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create(['name' => 'Old Name']);

        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $newName = 'Updated Exhibition Name';

        $page->navigate(route('admin.exhibitions.edit', $exhibition))
            ->assertSee('Редактировать выставку')
            ->clear('name')
            ->fill('name', $newName)
            ->submit();

        $exhibition = $exhibition->fresh();
        $this->assertEquals($exhibition->name, $newName);
    });

    it('doesnt allow to update an exhibition when the name is too long', function (): void {
        $user = User::factory()->create([
            'email'    => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create(['name' => 'Old Name']);

        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate(route('admin.exhibitions.edit', $exhibition))
            ->assertSee('Редактировать выставку')
            ->clear('name')
            ->fill('name', generateTextWithChars(260))
            ->submit()
            ->assertSee('Название не должно превышать 255 символов');
    });

    it('doesnt allow to update an exhibition when the buildin_folder_url is invalid', function (): void {
        $user = User::factory()->create([
            'email'    => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();

        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate(route('admin.exhibitions.edit', $exhibition))
            ->assertSee('Редактировать выставку')
            ->clear('buildin_folder_url')
            ->fill('buildin_folder_url', 'not-a-valid-url')
            ->submit()
            ->assertSee('Введите корректный URL папки');
    });

    it('doesnt allow to update an exhibition when ends_at is before starts_at', function (): void {
        $user = User::factory()->create([
            'email'    => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();

        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate(route('admin.exhibitions.edit', $exhibition))
            ->assertSee('Редактировать выставку')
            ->clear('starts_at')
            ->fill('starts_at', $this->endDate)
            ->clear('ends_at')
            ->fill('ends_at', $this->startDate)
            ->submit()
            ->assertSee('Дата окончания должна быть позже даты начала');
    });

    it('doesnt allow to update an exhibition when the location is too long', function (): void {
        $user = User::factory()->create([
            'email'    => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();

        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate(route('admin.exhibitions.edit', $exhibition))
            ->assertSee('Редактировать выставку')
            ->clear('location')
            ->fill('location', generateTextWithChars(260))
            ->submit()
            ->assertSee('Местоположение не должно превышать 255 символов');
    });

    it('allows to delete an exhibition', function (): void {
        $user = User::factory()->create([
            'email'    => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create(['name' => 'To Be Deleted']);

        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate(route('admin.exhibitions.edit', $exhibition))
            ->assertSee('Редактировать выставку')
            ->click('@delete-exhibition')
            ->click('@delete-btn')
            ->assertPathEndsWith(route('admin.exhibitions.index', absolute: false))
            ->assertDontSee('To Be Deleted');
    });
});
