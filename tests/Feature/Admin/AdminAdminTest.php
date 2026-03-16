<?php
declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Exhibition;
use App\Models\User;

use function Pest\Laravel\get;

describe('Admin Admin Page Test', function (): void {
    beforeEach(function (): void {
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
        $this->route = "/admin/exhibitions/{$this->exhibition->id}/admins";
    });

    it('allows SUPER ADMIN to enter the index page', function (): void {
        $this->actingAs($this->superAdmin)
            ->get($this->route)
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->component('admin/Admins/Index')
                    ->has('exhibition')
                    ->has('admins')
                    ->has('users')
            );
    });

    it('allows SUPER ADMIN to add an admin', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        $this->actingAs($this->superAdmin)
            ->put("{$this->route}/{$user->id}")
            ->assertRedirect();

        $user->refresh();
        $this->expect($user->role)->toBe(UserRole::ADMIN);
        $this->assertDatabaseHas('exhibition_user', [
            'exhibition_id' => $this->exhibition->id,
            'user_id'       => $user->id,
        ]);
    });

    it('allows SUPER ADMIN to delete an admin', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::ADMIN);
        $this->exhibition->users()->attach($user->id);

        $this->actingAs($this->superAdmin)
            ->delete("{$this->route}/{$user->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('exhibition_user', [
            'exhibition_id' => $this->exhibition->id,
            'user_id'       => $user->id,
        ]);
    });

    it('forbids ADMIN from entering the index page', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        $this->actingAs($admin)
            ->get($this->route)
            ->assertForbidden();
    });

    it('forbids ADMIN from adding an admin', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        $this->actingAs($admin)
            ->put("{$this->route}/{$user->id}")
            ->assertForbidden();

        $user->refresh();
        $this->expect($user->role)->toBe(UserRole::USER);
    });

    it('forbids ADMIN from deleting an admin', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);
        $target = User::factory()->create();
        $target->assignRole(UserRole::ADMIN);
        $this->exhibition->users()->attach($target->id);

        $this->actingAs($admin)
            ->delete("{$this->route}/{$target->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('exhibition_user', [
            'exhibition_id' => $this->exhibition->id,
            'user_id'       => $target->id,
        ]);
    });

    it('forbids EXPONENT from entering the index page', function (): void {
        $exponent = User::factory()->create();
        $exponent->assignRole(UserRole::EXPONENT);

        $this->actingAs($exponent)
            ->get($this->route)
            ->assertForbidden();
    });

    it('forbids EXPONENT from adding an admin', function (): void {
        $exponent = User::factory()->create();
        $exponent->assignRole(UserRole::EXPONENT);
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        $this->actingAs($exponent)
            ->put("{$this->route}/{$user->id}")
            ->assertForbidden();

        $user->refresh();
        $this->expect($user->role)->toBe(UserRole::USER);
    });

    it('forbids EXPONENT from deleting an admin', function (): void {
        $exponent = User::factory()->create();
        $exponent->assignRole(UserRole::EXPONENT);
        $target = User::factory()->create();
        $target->assignRole(UserRole::ADMIN);
        $this->exhibition->users()->attach($target->id);

        $this->actingAs($exponent)
            ->delete("{$this->route}/{$target->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('exhibition_user', [
            'exhibition_id' => $this->exhibition->id,
            'user_id'       => $target->id,
        ]);
    });

    it('forbids USER from entering the index page', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        $this->actingAs($user)
            ->get($this->route)
            ->assertForbidden();
    });

    it('forbids USER from adding an admin', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);
        $target = User::factory()->create();
        $target->assignRole(UserRole::USER);

        $this->actingAs($user)
            ->put("{$this->route}/{$target->id}")
            ->assertForbidden();

        $target->refresh();
        $this->expect($target->role)->toBe(UserRole::USER);
    });

    it('forbids USER from deleting an admin', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);
        $target = User::factory()->create();
        $target->assignRole(UserRole::ADMIN);
        $this->exhibition->users()->attach($target->id);

        $this->actingAs($user)
            ->delete("{$this->route}/{$target->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('exhibition_user', [
            'exhibition_id' => $this->exhibition->id,
            'user_id'       => $target->id,
        ]);
    });

    it('forbids guest users from entering the index page', function (): void {
        get($this->route)
            ->assertRedirect(route('login'));
    });

    it('forbids guest users from adding an admin', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        \Pest\Laravel\put("{$this->route}/{$user->id}")
            ->assertRedirect(route('login'));

        $user->refresh();
        $this->expect($user->role)->toBe(UserRole::USER);
    });

    it('forbids guest users from deleting an admin', function (): void {
        $target = User::factory()->create();
        $target->assignRole(UserRole::ADMIN);
        $this->exhibition->users()->attach($target->id);

        \Pest\Laravel\delete("{$this->route}/{$target->id}")
            ->assertRedirect(route('login'));

        $this->assertDatabaseHas('exhibition_user', [
            'exhibition_id' => $this->exhibition->id,
            'user_id'       => $target->id,
        ]);
    });

    it('can assign one admin to multiple exhibitions', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        $exhibition2 = Exhibition::factory()->create();

        $this->actingAs($this->superAdmin)
            ->put("{$this->route}/{$user->id}")
            ->assertRedirect();

        $this->actingAs($this->superAdmin)
            ->put("/admin/exhibitions/{$exhibition2->id}/admins/{$user->id}")
            ->assertRedirect();

        $user->refresh();
        $this->expect($user->exhibitions)->toHaveCount(2)
            ->and($user->role)->toBe(UserRole::ADMIN);
    });

    it('does not downgrade the admin to the user after deleting them if they still have another exhibition assigned', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::ADMIN);

        $exhibition2 = Exhibition::factory()->create();
        $this->exhibition->users()->attach($user->id);
        $exhibition2->users()->attach($user->id);

        $this->actingAs($this->superAdmin)
            ->delete("{$this->route}/{$user->id}")
            ->assertRedirect();

        $user->refresh();
        $this->expect($user->role)->toBe(UserRole::ADMIN);
        $this->assertDatabaseHas('exhibition_user', [
            'exhibition_id' => $exhibition2->id,
            'user_id'       => $user->id,
        ]);
    });

    it('downgrades the admin to the user after deleting them if they do not have another exhibition assigned', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::ADMIN);
        $this->exhibition->users()->attach($user->id);

        $this->actingAs($this->superAdmin)
            ->delete("{$this->route}/{$user->id}")
            ->assertRedirect();

        $user->refresh();
        $this->expect($user->role)->toBe(UserRole::USER);
        $this->assertDatabaseMissing('exhibition_user', [
            'exhibition_id' => $this->exhibition->id,
            'user_id'       => $user->id,
        ]);
    });

    it('upgrades the user to the admin after assigning an exhibition to it', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        $this->actingAs($this->superAdmin)
            ->put("{$this->route}/{$user->id}")
            ->assertRedirect();

        $user->refresh();
        $this->expect($user->role)->toBe(UserRole::ADMIN);
        $this->assertDatabaseHas('exhibition_user', [
            'exhibition_id' => $this->exhibition->id,
            'user_id'       => $user->id,
        ]);
    });
});
