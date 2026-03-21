<?php
declare(strict_types=1);

use App\Enums\FollowupStatus;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Exhibition;
use App\Models\Followup;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;

describe('Admin Company Followup Tests', function (): void {
    beforeEach(function (): void {
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
        $this->company = Company::factory()->for($this->exhibition)->create();
        $this->route = "/admin/companies/{$this->company->id}/followups";
        $this->validData = [
            'name'        => 'Test Followup',
            'description' => 'This is a valid description for the followup.',
            'comment'     => 'This is a valid comment for the followup.',
        ];
    });

    it('renders the company followups index page', function (): void {
        Followup::factory()->for($this->company)->create(['status' => FollowupStatus::COMPLETED]);

        actingAs($this->superAdmin)
            ->get($this->route)
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->component('admin/CompanyFollowups/Index')
                    ->has('followups')
                    ->has('company')
            );
    });

    it('displays only the followups that belong to this company', function (): void {
        $otherCompany = Company::factory()->for($this->exhibition)->create();

        Followup::factory()->for($this->company)->create(['status' => FollowupStatus::COMPLETED]);
        Followup::factory()->for($this->company)->create(['status' => FollowupStatus::COMPLETED]);
        Followup::factory()->for($otherCompany)->create(['status' => FollowupStatus::COMPLETED]);

        actingAs($this->superAdmin)
            ->get($this->route)
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->has('followups', 2)
            );
    });

    it('displays only completed followups', function (): void {
        Followup::factory()->for($this->company)->create(['status' => FollowupStatus::COMPLETED]);
        Followup::factory()->for($this->company)->create(['status' => FollowupStatus::INCOMPLETE]);

        actingAs($this->superAdmin)
            ->get($this->route)
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->has('followups', 1)
            );
    });

    it('creates only a completed followup', function (): void {
        actingAs($this->superAdmin)
            ->post($this->route, $this->validData)
            ->assertRedirect($this->route);

        assertDatabaseHas('followups', [
            'company_id' => $this->company->id,
            'name'       => $this->validData['name'],
            'status'     => FollowupStatus::COMPLETED->value,
        ]);
    });

    // ── Validation ──

    it('validates required fields on store', function (): void {
        actingAs($this->superAdmin)
            ->post($this->route, [])
            ->assertSessionHasErrors(['name', 'description', 'comment']);
    });

    it('validates name max length on store', function (): void {
        actingAs($this->superAdmin)
            ->post($this->route, array_merge($this->validData, [
                'name' => str_repeat('a', 201),
            ]))
            ->assertSessionHasErrors('name');
    });

    it('validates description min length on store', function (): void {
        actingAs($this->superAdmin)
            ->post($this->route, array_merge($this->validData, [
                'description' => 'Short',
            ]))
            ->assertSessionHasErrors('description');
    });

    it('validates description max length on store', function (): void {
        actingAs($this->superAdmin)
            ->post($this->route, array_merge($this->validData, [
                'description' => str_repeat('a', 5001),
            ]))
            ->assertSessionHasErrors('description');
    });

    it('validates comment min length on store', function (): void {
        actingAs($this->superAdmin)
            ->post($this->route, array_merge($this->validData, [
                'comment' => 'Short',
            ]))
            ->assertSessionHasErrors('comment');
    });

    it('validates comment max length on store', function (): void {
        actingAs($this->superAdmin)
            ->post($this->route, array_merge($this->validData, [
                'comment' => str_repeat('a', 5001),
            ]))
            ->assertSessionHasErrors('comment');
    });

    it('validates name max length on update', function (): void {
        $followup = Followup::factory()->for($this->company)->create(['status' => FollowupStatus::COMPLETED]);

        actingAs($this->superAdmin)
            ->put("{$this->route}/{$followup->id}", [
                'name' => str_repeat('a', 201),
            ])
            ->assertSessionHasErrors('name');
    });

    it('validates description min length on update', function (): void {
        $followup = Followup::factory()->for($this->company)->create(['status' => FollowupStatus::COMPLETED]);

        actingAs($this->superAdmin)
            ->put("{$this->route}/{$followup->id}", [
                'description' => 'Short',
            ])
            ->assertSessionHasErrors('description');
    });

    it('validates comment min length on update', function (): void {
        $followup = Followup::factory()->for($this->company)->create(['status' => FollowupStatus::COMPLETED]);

        actingAs($this->superAdmin)
            ->put("{$this->route}/{$followup->id}", [
                'comment' => 'Short',
            ])
            ->assertSessionHasErrors('comment');
    });

    // ── Admin with access ──

    it('allows to view the followups of this company only to the admin that is assigned to the exhibition that the company belongs to', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);
        $this->exhibition->users()->attach($admin->id);

        actingAs($admin)
            ->get($this->route)
            ->assertOk();
    });

    it('allows to create the followup only to the admin that is assigned to this exhibition', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);
        $this->exhibition->users()->attach($admin->id);

        actingAs($admin)
            ->post($this->route, $this->validData)
            ->assertRedirect($this->route);

        assertDatabaseHas('followups', [
            'company_id' => $this->company->id,
            'name'       => $this->validData['name'],
        ]);
    });

    it('allows to update the followup only to the admin that is assigned to this exhibition', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);
        $this->exhibition->users()->attach($admin->id);
        $followup = Followup::factory()->for($this->company)->create(['status' => FollowupStatus::COMPLETED]);

        actingAs($admin)
            ->put("{$this->route}/{$followup->id}", ['name' => 'Updated Name'])
            ->assertRedirect($this->route);

        assertDatabaseHas('followups', [
            'id'   => $followup->id,
            'name' => 'Updated Name',
        ]);
    });

    it('allows to delete the followup only to the admin that is assigned to this exhibition', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);
        $this->exhibition->users()->attach($admin->id);
        $followup = Followup::factory()->for($this->company)->create(['status' => FollowupStatus::COMPLETED]);

        actingAs($admin)
            ->delete("{$this->route}/{$followup->id}")
            ->assertRedirect($this->route);

        assertDatabaseMissing('followups', ['id' => $followup->id]);
    });

    // ── Super admin ──

    it('allows to view any followups to the super admin', function (): void {
        actingAs($this->superAdmin)
            ->get($this->route)
            ->assertOk();
    });

    it('allows to create any followups to the super admin', function (): void {
        actingAs($this->superAdmin)
            ->post($this->route, $this->validData)
            ->assertRedirect($this->route);

        assertDatabaseHas('followups', [
            'company_id' => $this->company->id,
            'name'       => $this->validData['name'],
        ]);
    });

    it('allows to update any followups to the super admin', function (): void {
        $followup = Followup::factory()->for($this->company)->create(['status' => FollowupStatus::COMPLETED]);

        actingAs($this->superAdmin)
            ->put("{$this->route}/{$followup->id}", ['name' => 'Updated Name'])
            ->assertRedirect($this->route);

        assertDatabaseHas('followups', [
            'id'   => $followup->id,
            'name' => 'Updated Name',
        ]);
    });

    it('allows to delete any followups to the super admin', function (): void {
        $followup = Followup::factory()->for($this->company)->create(['status' => FollowupStatus::COMPLETED]);

        actingAs($this->superAdmin)
            ->delete("{$this->route}/{$followup->id}")
            ->assertRedirect($this->route);

        assertDatabaseMissing('followups', ['id' => $followup->id]);
    });

    // ── Exponent ──

    it('forbids exponent from viewing any followups', function (): void {
        $exponent = User::factory()->create();
        $exponent->assignRole(UserRole::EXPONENT);

        actingAs($exponent)
            ->get($this->route)
            ->assertForbidden();
    });

    it('forbids exponent from creating any followups', function (): void {
        $exponent = User::factory()->create();
        $exponent->assignRole(UserRole::EXPONENT);

        actingAs($exponent)
            ->post($this->route, $this->validData)
            ->assertForbidden();

        assertDatabaseMissing('followups', ['name' => $this->validData['name']]);
    });

    it('forbids exponent from updating any followups', function (): void {
        $exponent = User::factory()->create();
        $exponent->assignRole(UserRole::EXPONENT);
        $followup = Followup::factory()->for($this->company)->create(['status' => FollowupStatus::COMPLETED]);

        actingAs($exponent)
            ->put("{$this->route}/{$followup->id}", ['name' => 'Updated Name'])
            ->assertForbidden();

        assertDatabaseMissing('followups', ['id' => $followup->id, 'name' => 'Updated Name']);
    });

    it('forbids exponent from deleting any followups', function (): void {
        $exponent = User::factory()->create();
        $exponent->assignRole(UserRole::EXPONENT);
        $followup = Followup::factory()->for($this->company)->create(['status' => FollowupStatus::COMPLETED]);

        actingAs($exponent)
            ->delete("{$this->route}/{$followup->id}")
            ->assertForbidden();

        assertDatabaseHas('followups', ['id' => $followup->id]);
    });

    // ── User ──

    it('forbids user from viewing any followups', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        actingAs($user)
            ->get($this->route)
            ->assertForbidden();
    });

    it('forbids user from creating any followups', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        actingAs($user)
            ->post($this->route, $this->validData)
            ->assertForbidden();

        assertDatabaseMissing('followups', ['name' => $this->validData['name']]);
    });

    it('forbids user from updating any followups', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);
        $followup = Followup::factory()->for($this->company)->create(['status' => FollowupStatus::COMPLETED]);

        actingAs($user)
            ->put("{$this->route}/{$followup->id}", ['name' => 'Updated Name'])
            ->assertForbidden();

        assertDatabaseMissing('followups', ['id' => $followup->id, 'name' => 'Updated Name']);
    });

    it('forbids user from deleting any followups', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);
        $followup = Followup::factory()->for($this->company)->create(['status' => FollowupStatus::COMPLETED]);

        actingAs($user)
            ->delete("{$this->route}/{$followup->id}")
            ->assertForbidden();

        assertDatabaseHas('followups', ['id' => $followup->id]);
    });

    // ── Guest ──

    it('forbids guest users from viewing any followups', function (): void {
        get($this->route)->assertRedirect(route('login'));
    });

    it('forbids guest users from creating any followups', function (): void {
        \Pest\Laravel\post($this->route, $this->validData)
            ->assertRedirect(route('login'));

        assertDatabaseMissing('followups', ['name' => $this->validData['name']]);
    });

    it('forbids guest users from updating any followups', function (): void {
        $followup = Followup::factory()->for($this->company)->create(['status' => FollowupStatus::COMPLETED]);

        \Pest\Laravel\put("{$this->route}/{$followup->id}", ['name' => 'Updated Name'])
            ->assertRedirect(route('login'));

        assertDatabaseMissing('followups', ['id' => $followup->id, 'name' => 'Updated Name']);
    });

    it('forbids guest users from deleting any followups', function (): void {
        $followup = Followup::factory()->for($this->company)->create(['status' => FollowupStatus::COMPLETED]);

        \Pest\Laravel\delete("{$this->route}/{$followup->id}")
            ->assertRedirect(route('login'));

        assertDatabaseHas('followups', ['id' => $followup->id]);
    });
})->group('feature');
