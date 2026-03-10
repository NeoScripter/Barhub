<?php

declare(strict_types=1);

use App\Enums\FollowupStatus;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Exhibition;
use App\Models\Followup;
use App\Models\Service;
use App\Models\User;

describe('Admin Followup Browser Tests', function (): void {

    it('renders the followups index page', function () {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $service = Service::factory()->for($company)->create();
        Followup::factory()->for($service)->for($user)->count(4)->create();

        $route = "/admin/exhibitions/{$exhibition->id}/followups";

        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route);
        $page->assertSee('Работа с партнерами');
    });

    it('renders the followups edit page', function () {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $service = Service::factory()->for($company)->create();
        $followup = Followup::factory()
            ->for($service)
            ->for($user)
            ->create(['status' => FollowupStatus::IMCOMPLETE]);

        $route = "/admin/exhibitions/{$exhibition->id}/followups";

        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->click("@edit-followup-{$followup->id}")
            ->assertSee('Название компании');
    });

    it('successfully completes a followup', function () {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $service = Service::factory()->for($company)->create();
        $followup = Followup::factory()
            ->for($service)
            ->for($user)
            ->create(['status' => FollowupStatus::IMCOMPLETE]);

        $route = "/admin/exhibitions/{$exhibition->id}/followups";

        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->click("@edit-followup-{$followup->id}")
            ->assertSee('Название компании')
            ->submit()
            ->assertPathEndsWith($route);

        $this->assertDatabaseHas('followups', [
            'id'     => $followup->id,
            'status' => FollowupStatus::COMPLETED->value,
        ]);
    });

    it('displays only the followups that belong to this exhibition', function () {
        $user = User::factory()->create([
            'email' => 'admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::ADMIN);

        $exhibition = Exhibition::factory()->create();
        $exhibition->users()->attach($user->id);
        $otherExhibition = Exhibition::factory()->create();

        $company = Company::factory()->for($exhibition)->create();
        $otherCompany = Company::factory()->for($otherExhibition)->create();

        $service = Service::factory()->for($company)->create([
            'description' => 'Belongs To This Exhibition',
        ]);
        $otherService = Service::factory()->for($otherCompany)->create([
            'description' => 'Belongs To Other Exhibition',
        ]);

        Followup::factory()->for($service)->for($user)->create([
            'status'  => FollowupStatus::IMCOMPLETE,
        ]);
        Followup::factory()->for($otherService)->for($user)->create([
            'status'  => FollowupStatus::IMCOMPLETE,
        ]);

        $route = "/admin/exhibitions/{$exhibition->id}/followups";

        $page = visit('/login');
        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee('Belongs To This Exhibition')
            ->assertDontSee('Belongs To Other Exhibition');
    });
})->group('browser');

describe('Admin Followup Feature Tests', function (): void {
    beforeEach(function (): void {
        $this->superAdmin = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
        $this->route = "/admin/exhibitions/{$this->exhibition->id}/followups";
    });

    it('sorts followups by service name in desc order', function () {
        $company = Company::factory()->for($this->exhibition)->create();
        Service::factory()
            ->count(3)
            ->for($company)
            ->has(Followup::factory()->for($this->superAdmin)->state(['status' => FollowupStatus::IMCOMPLETE]))
            ->sequence(
                ['name' => 'Zebra'],
                ['name' => 'Alpha'],
                ['name' => 'Beta'],
            )
            ->create();

        $followups = $this->actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.followups.index', [
                'exhibition' => $this->exhibition,
                'sort'       => '-service.name',
            ]))
            ->assertOk()
            ->viewData('page')['props']['followups']['data'];

        expect($followups[0]['service']['name'])->toBe('Zebra')
            ->and($followups[1]['service']['name'])->toBe('Beta')
            ->and($followups[2]['service']['name'])->toBe('Alpha');
    });

    it('sorts followups by service name in asc order', function () {
        $company = Company::factory()->for($this->exhibition)->create();
        Service::factory()
            ->count(3)
            ->for($company)
            ->has(Followup::factory()->for($this->superAdmin)->state(['status' => FollowupStatus::IMCOMPLETE]))
            ->sequence(
                ['name' => 'Zebra'],
                ['name' => 'Alpha'],
                ['name' => 'Beta'],
            )
            ->create();

        $followups = $this->actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.followups.index', [
                'exhibition' => $this->exhibition,
                'sort'       => 'service.name',
            ]))
            ->assertOk()
            ->viewData('page')['props']['followups']['data'];

        expect($followups[0]['service']['name'])->toBe('Alpha')
            ->and($followups[1]['service']['name'])->toBe('Beta')
            ->and($followups[2]['service']['name'])->toBe('Zebra');
    });

    it('allows super admin to enter this page', function () {
        $this->actingAs($this->superAdmin)
            ->get($this->route)
            ->assertOk();
    });

    it('allows super admin to complete a followup whose status is incomplete', function () {
        $company  = Company::factory()->for($this->exhibition)->create();
        $service  = Service::factory()->for($company)->create();
        $followup = Followup::factory()
            ->for($service)
            ->for($this->superAdmin)
            ->create(['status' => FollowupStatus::IMCOMPLETE]);

        $this->actingAs($this->superAdmin)
            ->patch("{$this->route}/{$followup->id}")
            ->assertRedirect($this->route);

        $this->assertDatabaseHas('followups', [
            'id'     => $followup->id,
            'status' => FollowupStatus::COMPLETED->value,
        ]);
    });

    it('forbids completing a followup whose status is already completed', function () {
        $company  = Company::factory()->for($this->exhibition)->create();
        $service  = Service::factory()->for($company)->create();
        $followup = Followup::factory()
            ->for($service)
            ->for($this->superAdmin)
            ->create(['status' => FollowupStatus::COMPLETED]);

        $this->actingAs($this->superAdmin)
            ->patch("{$this->route}/{$followup->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('followups', [
            'id'     => $followup->id,
            'status' => FollowupStatus::COMPLETED->value,
        ]);
    });

    it('allows admins with access to this exhibition to enter this page', function () {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);
        $this->exhibition->users()->attach($admin->id);

        $this->actingAs($admin)
            ->get($this->route)
            ->assertOk();
    });

    it('forbids admins without access to this exhibition from entering this page', function () {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        $this->actingAs($admin)
            ->get($this->route)
            ->assertForbidden();
    });

    it('forbids admins without access from completing a followup', function () {
        $admin   = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);
        $company  = Company::factory()->for($this->exhibition)->create();
        $service  = Service::factory()->for($company)->create();
        $followup = Followup::factory()
            ->for($service)
            ->for($this->superAdmin)
            ->create(['status' => FollowupStatus::IMCOMPLETE]);

        $this->actingAs($admin)
            ->patch("{$this->route}/{$followup->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('followups', [
            'id'     => $followup->id,
            'status' => FollowupStatus::IMCOMPLETE->value,
        ]);
    });

    it('forbids exponents from entering this page', function () {
        $exponent = User::factory()->create();
        $exponent->assignRole(UserRole::EXPONENT);

        $this->actingAs($exponent)
            ->get($this->route)
            ->assertForbidden();
    });

    it('forbids exponents from completing a followup', function () {
        $exponent = User::factory()->create();
        $exponent->assignRole(UserRole::EXPONENT);
        $company  = Company::factory()->for($this->exhibition)->create();
        $service  = Service::factory()->for($company)->create();
        $followup = Followup::factory()
            ->for($service)
            ->for($this->superAdmin)
            ->create(['status' => FollowupStatus::IMCOMPLETE]);

        $this->actingAs($exponent)
            ->patch("{$this->route}/{$followup->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('followups', [
            'id'     => $followup->id,
            'status' => FollowupStatus::IMCOMPLETE->value,
        ]);
    });

    it('forbids users from entering this page', function () {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        $this->actingAs($user)
            ->get($this->route)
            ->assertForbidden();
    });

    it('forbids users from completing a followup', function () {
        $user    = User::factory()->create();
        $user->assignRole(UserRole::USER);
        $company  = Company::factory()->for($this->exhibition)->create();
        $service  = Service::factory()->for($company)->create();
        $followup = Followup::factory()
            ->for($service)
            ->for($this->superAdmin)
            ->create(['status' => FollowupStatus::IMCOMPLETE]);

        $this->actingAs($user)
            ->patch("{$this->route}/{$followup->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('followups', [
            'id'     => $followup->id,
            'status' => FollowupStatus::IMCOMPLETE->value,
        ]);
    });

    it('forbids unregistered users to enter this page', function () {
        $this->get($this->route)
            ->assertRedirect('/login');
    });

    it('forbids unregistered users from completing a followup', function () {
        $company  = Company::factory()->for($this->exhibition)->create();
        $service  = Service::factory()->for($company)->create();
        $followup = Followup::factory()
            ->for($service)
            ->for($this->superAdmin)
            ->create(['status' => FollowupStatus::IMCOMPLETE]);

        $this->patch("{$this->route}/{$followup->id}")
            ->assertRedirect('/login');

        $this->assertDatabaseHas('followups', [
            'id'     => $followup->id,
            'status' => FollowupStatus::IMCOMPLETE->value,
        ]);
    });

    it('does not display completed followups on the index page', function () {
        $company = Company::factory()->for($this->exhibition)->create();
        $service = Service::factory()->for($company)->create();

        Followup::factory()->for($service)->for($this->superAdmin)->create(['status' => FollowupStatus::IMCOMPLETE]);
        Followup::factory()->for($service)->for($this->superAdmin)->create(['status' => FollowupStatus::COMPLETED]);

        $followups = $this->actingAs($this->superAdmin)
            ->get($this->route)
            ->assertOk()
            ->viewData('page')['props']['followups']['data'];

        expect($followups)->toHaveCount(1)
            ->and($followups[0]['status'])->toBe(FollowupStatus::IMCOMPLETE->label());
    });
})->group('feature');
