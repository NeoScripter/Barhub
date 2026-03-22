<?php
declare(strict_types=1);

use App\Enums\FollowupStatus;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Exhibition;
use App\Models\Followup;
use App\Models\Service;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;

describe('Exponent Followup Test', function (): void {
    beforeEach(function (): void {
        $this->exhibition = Exhibition::factory()->create();
        $this->company = Company::factory()->for($this->exhibition)->create();
        $this->exponent = User::factory()->for($this->company)->create();
        $this->exponent->assignRole(UserRole::EXPONENT);
    });

    it('renders the followup index page', function (): void {
        actingAs($this->exponent)
            ->get(route('exponent.followups.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->component('exponent/Followups/Index')
                    ->has('followups')
                    ->has('services')
                    ->has('company')
            );
    });

    it('displays only the services assigned to the exhibition that owns the exponents company', function (): void {
        $otherExhibition = Exhibition::factory()->create();

        Service::factory(3)->for($this->exhibition)->create();
        Service::factory(2)->for($otherExhibition)->create();

        actingAs($this->exponent)
            ->get(route('exponent.followups.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->has('services', 3)
            );
    });

    it('displays only the followups that belong to the exponents company', function (): void {
        $otherCompany = Company::factory()->for($this->exhibition)->create();

        Followup::factory(2)->for($this->company)->create(['status' => FollowupStatus::INCOMPLETE]);
        Followup::factory(3)->for($otherCompany)->create(['status' => FollowupStatus::INCOMPLETE]);

        actingAs($this->exponent)
            ->get(route('exponent.followups.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->has('followups', 2)
            );
    });

    it('successfully creates a followup with the status incomplete', function (): void {
        $service = Service::factory()->for($this->exhibition)->create();

        actingAs($this->exponent)
            ->post(route('exponent.followups.store'), [
                'service_id' => $service->id,
                'comment'    => 'This is a valid comment for the followup request.',
            ])
            ->assertRedirect(route('exponent.followups.index'));

        assertDatabaseHas('followups', [
            'company_id' => $this->company->id,
            'user_id'    => $this->exponent->id,
            'name'       => $service->name,
            'status'     => FollowupStatus::INCOMPLETE->value,
        ]);
    });

    it('allows only an exponent to create a followup via this route', function (): void {
        $service = Service::factory()->for($this->exhibition)->create();

        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        actingAs($admin)
            ->post(route('exponent.followups.store'), [
                'service_id' => $service->id,
                'comment'    => 'This is a valid comment.',
            ])
            ->assertForbidden();

        \Pest\Laravel\assertDatabaseMissing('followups', [
            'name' => $service->name,
        ]);
    });

    it('allows EXPONENT role to access exponent followups index', function (): void {
        actingAs($this->exponent)
            ->get(route('exponent.followups.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->component('exponent/Followups/Index')
            );
    });

    it('forbids USER role from accessing exponent followups', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        actingAs($user)
            ->get(route('exponent.followups.index'))
            ->assertForbidden();
    });

    it('forbids ADMIN role from accessing exponent followups', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        actingAs($admin)
            ->get(route('exponent.followups.index'))
            ->assertForbidden();
    });

    it('forbids SUPER_ADMIN role from accessing exponent followups', function (): void {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(UserRole::SUPER_ADMIN);

        actingAs($superAdmin)
            ->get(route('exponent.followups.index'))
            ->assertForbidden();
    });

    it('redirects unauthenticated users to login page', function (): void {
        get(route('exponent.followups.index'))
            ->assertRedirect(route('login'));
    });
});
