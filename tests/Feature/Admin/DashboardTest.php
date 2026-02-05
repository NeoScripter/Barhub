<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Spatie\Permission\Models\Role;

beforeEach(fn () => $this->seed(PermissionsSeeder::class));

describe('Admin Panel Access Control', function () {

    test('guest users cannot access admin panel', function () {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    });

    test('users with USER role cannot access admin panel', function () {
        $role = Role::findOrCreate(UserRole::USER->value);
        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)
            ->get(route('admin.dashboard'));

        $response->assertForbidden(); // 403
    });

    test('users with EXPONENT role cannot access admin panel', function () {
        $role = Role::findOrCreate(UserRole::EXPONENT->value);
        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)
            ->get(route('admin.dashboard'));

        $response->assertForbidden(); // 403
    });

    test('users with ADMIN role can access admin panel', function () {
        $role = Role::findOrCreate(UserRole::ADMIN->value);
        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)
            ->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('admin/Dashboard')
        );
    });

    test('users with SUPER_ADMIN role can access admin panel', function () {
        $role = Role::findOrCreate(UserRole::SUPER_ADMIN->value);
        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)
            ->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('admin/Dashboard')
        );
    });

    test('users without any role cannot access admin panel', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('admin.dashboard'));

        $response->assertForbidden(); // 403
    });
});
