<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

describe('Admin Panel Access Control', function (): void {

    it('redirects guest users to login', function (): void {
        get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    });

    it('forbids USER role from accessing admin panel', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    });

    it('forbids EXPONENT role from accessing admin panel', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::EXPONENT);

        actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    });

    it('redirects unauthenticated users to login page', function (): void {
        get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    });

    it('allows ADMIN role to access admin panel', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->component('admin/Dashboard/Dashboard')
            );
    });

    it('allows SUPER_ADMIN role to access admin panel', function (): void {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(UserRole::SUPER_ADMIN);

        actingAs($superAdmin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->component('admin/Dashboard/Dashboard')
            );
    });
});
