<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Exhibition;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

describe('Exhibition Page Permissions', function (): void {
    beforeEach(function(): void {
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
        \Pest\Laravel\post(route('admin.exhibitions.store'), [
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

        \Pest\Laravel\put(route('admin.exhibitions.update', $exhibition), [
            'name' => 'Updated Name',
        ])
            ->assertRedirect(route('login'));
    });

    it('forbids guest users from deleting an exhibition', function (): void {
        $exhibition = Exhibition::factory()->create();

        \Pest\Laravel\delete(route('admin.exhibitions.destroy', $exhibition))
            ->assertRedirect(route('login'));

        assertDatabaseHas('exhibitions', ['id' => $exhibition->id]);
    });
});
