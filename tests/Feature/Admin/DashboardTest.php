<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Exhibition;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

describe('Admin Panel Access Control', function (): void {

    it('makes an exhibition active for an admin', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);
        $exhibition = Exhibition::factory()->create();
        $admin->exhibitions()->attach($exhibition->id);

        actingAs($admin)
            ->put(route('admin.dashboard.update', $exhibition->id))
            ->assertRedirect();

        $admin->refresh();
        expect($admin->active_exhibition_id)->toBe($exhibition->id);
    });

    it('forbids from activating an exhibition for non-admin users', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);
        $exhibition = Exhibition::factory()->create();

        actingAs($user)
            ->put(route('admin.dashboard.update', $exhibition->id))
            ->assertForbidden();

        $user->refresh();
        expect($user->active_exhibition_id)->toBeNull();
    });

    it('redirects guest users to login', function (): void {
        get(route('admin.dashboard.index'))
            ->assertRedirect(route('login'));
    });

    it('forbids USER role from accessing admin panel', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        actingAs($user)
            ->get(route('admin.dashboard.index'))
            ->assertForbidden();
    });

    it('forbids EXPONENT role from accessing admin panel', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::EXPONENT);

        actingAs($user)
            ->get(route('admin.dashboard.index'))
            ->assertForbidden();
    });

    it('redirects unauthenticated users to login page', function (): void {
        get(route('admin.dashboard.index'))
            ->assertRedirect(route('login'));
    });

    it('forbits admin without exhibitions from accessing admin panel', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        actingAs($admin)
            ->get(route('admin.dashboard.index'))
            ->assertForbidden();
    });

    it('allows admin with exhibitions to access admin panel', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);
        $exhibition = Exhibition::factory()->create();

        $exhibition->users()->attach($admin->id);

        actingAs($admin)
            ->get(route('admin.dashboard.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->component('admin/Dashboard/Dashboard')
            );
    });

    it('allows SUPER_ADMIN role to access admin panel', function (): void {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(UserRole::SUPER_ADMIN);

        actingAs($superAdmin)
            ->get(route('admin.dashboard.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->component('admin/Dashboard/Dashboard')
            );
    });
});
