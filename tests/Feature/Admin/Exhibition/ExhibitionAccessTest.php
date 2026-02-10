<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Exhibition;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

describe('Exhibition Panel Permissions', function (): void {

    test('super admin can see all exhibitions', function (): void {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(UserRole::SUPER_ADMIN);

        $exhibitions = Exhibition::factory(10)->create();

        $response = actingAs($superAdmin)
            ->get(route('admin.exhibitions.index'));

        $response
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->component('admin/Exhibitions/Exhibitions')
            );

        $exhibitions->each(
            fn($exhibition) =>
            $response->assertSee($exhibition->name)
        );
    });

    test('admin can see only exhibitions assigned to them', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        // Create exhibitions assigned to this admin
        $assignedExhibitions = Exhibition::factory(3)->create();
        $assignedExhibitions->each(
            fn($exhibition) =>
            $exhibition->users()->attach($admin)
        );

        // Create exhibitions NOT assigned to this admin
        $unassignedExhibitions = Exhibition::factory(2)->create();

        $response = actingAs($admin)
            ->get(route('admin.exhibitions.index'));

        $response
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->component('admin/Exhibitions/Exhibitions')
            );

        // Should see assigned exhibitions
        $assignedExhibitions->each(
            fn($exhibition) =>
            $response->assertSee($exhibition->name)
        );

        // Should NOT see unassigned exhibitions
        $unassignedExhibitions->each(
            fn($exhibition) =>
            $response->assertDontSee($exhibition->name)
        );
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
});
