<?php
declare(strict_types=1);

use App\Enums\FollowupStatus;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Exhibition;
use App\Models\Followup;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

describe('Admin Followup Tests', function (): void {
    beforeEach(function (): void {
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
        $this->superAdmin->setActiveExhibition($this->exhibition->id);
        $this->company = Company::factory()->for($this->exhibition)->create();
        $this->route = route('admin.followups.index');
    });

    it('renders the followups index page', function (): void {
        actingAs($this->superAdmin)
            ->get($this->route)
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->component('admin/Followups/Index')
                    ->has('followups')
            );
    });

    it('displays only incomplete followups', function (): void {
        Followup::factory()->for($this->company)->create(['status' => FollowupStatus::IMCOMPLETE]);
        Followup::factory()->for($this->company)->create(['status' => FollowupStatus::COMPLETED]);

        actingAs($this->superAdmin)
            ->get($this->route)
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->has('followups.data', 1)
            );
    });

    it('successfully updates the status of a followup', function (): void {
        $followup = Followup::factory()->for($this->company)->create(['status' => FollowupStatus::IMCOMPLETE]);

        actingAs($this->superAdmin)
            ->patch(route('admin.followups.update', $followup))
            ->assertRedirect($this->route);

        \Pest\Laravel\assertDatabaseHas('followups', [
            'id'     => $followup->id,
            'status' => FollowupStatus::COMPLETED->value,
        ]);
    });

    it('successfully displays the number of incomplete followups for this exhibition', function (): void {
        Followup::factory(3)->for($this->company)->create(['status' => FollowupStatus::IMCOMPLETE]);
        Followup::factory(2)->for($this->company)->create(['status' => FollowupStatus::COMPLETED]);

        actingAs($this->superAdmin)
            ->get($this->route)
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->has('followups.data', 3)
            );
    });

    it('displays only the incomplete followups for the active exhibition for the admin', function (): void {
        $otherExhibition = Exhibition::factory()->create();
        $otherCompany = Company::factory()->for($otherExhibition)->create();

        Followup::factory(2)->for($this->company)->create(['status' => FollowupStatus::IMCOMPLETE]);
        Followup::factory(3)->for($otherCompany)->create(['status' => FollowupStatus::IMCOMPLETE]);

        actingAs($this->superAdmin)
            ->get($this->route)
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->has('followups.data', 2)
            );
    });

    it('forbids admins without access to any exhibition from entering this page', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        actingAs($admin)
            ->get($this->route)
            ->assertForbidden();
    });

    it('forbids exponents from entering this page', function (): void {
        $exponent = User::factory()->create();
        $exponent->assignRole(UserRole::EXPONENT);

        actingAs($exponent)
            ->get($this->route)
            ->assertForbidden();
    });

    it('forbids users from entering this page', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        actingAs($user)
            ->get($this->route)
            ->assertForbidden();
    });

    it('forbids guest users from entering this page', function (): void {
        get($this->route)
            ->assertRedirect(route('login'));
    });

    it('allows admins with access to an exhibition to enter this page', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);
        $this->exhibition->users()->attach($admin->id);
        $admin->setActiveExhibition($this->exhibition->id);

        actingAs($admin)
            ->get($this->route)
            ->assertOk();
    });

    it('allows super admin to enter this page', function (): void {
        actingAs($this->superAdmin)
            ->get($this->route)
            ->assertOk();
    });

    it('sorts the items based on company public name in ascending order', function (): void {
        $companyZ = Company::factory()->for($this->exhibition)->create(['public_name' => 'Zebra Co']);
        $companyA = Company::factory()->for($this->exhibition)->create(['public_name' => 'Alpha Co']);
        $companyB = Company::factory()->for($this->exhibition)->create(['public_name' => 'Beta Co']);

        Followup::factory()->for($companyZ)->create(['status' => FollowupStatus::IMCOMPLETE]);
        Followup::factory()->for($companyA)->create(['status' => FollowupStatus::IMCOMPLETE]);
        Followup::factory()->for($companyB)->create(['status' => FollowupStatus::IMCOMPLETE]);

        $data = actingAs($this->superAdmin)
            ->get(route('admin.followups.index', ['sort' => 'company.public_name']))
            ->assertOk()
            ->viewData('page')['props']['followups']['data'];

        expect($data[0]['company']['public_name'])->toBe('Alpha Co')
            ->and($data[1]['company']['public_name'])->toBe('Beta Co')
            ->and($data[2]['company']['public_name'])->toBe('Zebra Co');
    });

    it('sorts the items based on company public name in descending order', function (): void {
        $companyZ = Company::factory()->for($this->exhibition)->create(['public_name' => 'Zebra Co']);
        $companyA = Company::factory()->for($this->exhibition)->create(['public_name' => 'Alpha Co']);
        $companyB = Company::factory()->for($this->exhibition)->create(['public_name' => 'Beta Co']);

        Followup::factory()->for($companyZ)->create(['status' => FollowupStatus::IMCOMPLETE]);
        Followup::factory()->for($companyA)->create(['status' => FollowupStatus::IMCOMPLETE]);
        Followup::factory()->for($companyB)->create(['status' => FollowupStatus::IMCOMPLETE]);

        $data = actingAs($this->superAdmin)
            ->get(route('admin.followups.index', ['sort' => '-company.public_name']))
            ->assertOk()
            ->viewData('page')['props']['followups']['data'];

        expect($data[0]['company']['public_name'])->toBe('Zebra Co')
            ->and($data[1]['company']['public_name'])->toBe('Beta Co')
            ->and($data[2]['company']['public_name'])->toBe('Alpha Co');
    });

    it('sorts the items based on followup name in ascending order', function (): void {
        Followup::factory()->for($this->company)->create(['name' => 'Zebra Followup', 'status' => FollowupStatus::IMCOMPLETE]);
        Followup::factory()->for($this->company)->create(['name' => 'Alpha Followup', 'status' => FollowupStatus::IMCOMPLETE]);
        Followup::factory()->for($this->company)->create(['name' => 'Beta Followup', 'status' => FollowupStatus::IMCOMPLETE]);

        $data = actingAs($this->superAdmin)
            ->get(route('admin.followups.index', ['sort' => 'name']))
            ->assertOk()
            ->viewData('page')['props']['followups']['data'];

        expect($data[0]['name'])->toBe('Alpha Followup')
            ->and($data[1]['name'])->toBe('Beta Followup')
            ->and($data[2]['name'])->toBe('Zebra Followup');
    });

    it('sorts the items based on followup name in descending order', function (): void {
        Followup::factory()->for($this->company)->create(['name' => 'Zebra Followup', 'status' => FollowupStatus::IMCOMPLETE]);
        Followup::factory()->for($this->company)->create(['name' => 'Alpha Followup', 'status' => FollowupStatus::IMCOMPLETE]);
        Followup::factory()->for($this->company)->create(['name' => 'Beta Followup', 'status' => FollowupStatus::IMCOMPLETE]);

        $data = actingAs($this->superAdmin)
            ->get(route('admin.followups.index', ['sort' => '-name']))
            ->assertOk()
            ->viewData('page')['props']['followups']['data'];

        expect($data[0]['name'])->toBe('Zebra Followup')
            ->and($data[1]['name'])->toBe('Beta Followup')
            ->and($data[2]['name'])->toBe('Alpha Followup');
    });
})->group('feature');
