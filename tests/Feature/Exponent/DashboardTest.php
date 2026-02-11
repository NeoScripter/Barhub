<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

describe('Exponent Dashboard Access Control', function (): void {
    it('redirects guest users to login', function (): void {
        get(route('exponent.dashboard'))
            ->assertRedirect(route('login'));
    });

    it('redirects authenticated exponent to exponent dashboard after login', function (): void {
        $exponent = User::factory()->create();
        $exponent->assignRole(UserRole::EXPONENT);

        $this->post(route('login'), [
            'email' => $exponent->email,
            'password' => 'password',
        ])
            ->assertRedirect(route('exponent.dashboard'));
    });

    it('allows EXPONENT role to access exponent dashboard', function (): void {
        $exponent = User::factory()->create();
        $exponent->assignRole(UserRole::EXPONENT);

        actingAs($exponent)
            ->get(route('exponent.dashboard'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->component('exponent/Dashboard/Dashboard')
            );
    });

    it('forbids USER role from accessing exponent dashboard', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        actingAs($user)
            ->get(route('exponent.dashboard'))
            ->assertForbidden();
    });

    it('forbids ADMIN role from accessing exponent dashboard', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        actingAs($admin)
            ->get(route('exponent.dashboard'))
            ->assertForbidden();
    });

    it('forbids SUPER_ADMIN role from accessing exponent dashboard', function (): void {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(UserRole::SUPER_ADMIN);

        actingAs($superAdmin)
            ->get(route('exponent.dashboard'))
            ->assertForbidden();
    });

    it('redirects unauthenticated users to login page', function (): void {
        get(route('exponent.dashboard'))
            ->assertRedirect(route('login'));
    });
});
