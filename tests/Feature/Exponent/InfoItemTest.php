<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Exhibition;
use App\Models\InfoItem;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

describe('Exponent Info Item Test', function (): void {

    it('allows EXPONENT with company to enter this page', function (): void {
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $exponent = User::factory()->create();
        $exponent->assignRole(UserRole::EXPONENT);
        $exponent->company()->associate($company)->save();

        actingAs($exponent)
            ->get(route('exponent.info-items.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->component('exponent/InfoItems/Index')
                    ->has('infoItems')
            );
    });

    it('forbids EXPONENT without company from entering this page', function (): void {
        $exponent = User::factory()->create();
        $exponent->assignRole(UserRole::EXPONENT);

        actingAs($exponent)
            ->get(route('exponent.info-items.index'))
            ->assertNotFound();
    });

    it('forbids ADMIN from entering this page', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        actingAs($admin)
            ->get(route('exponent.info-items.index'))
            ->assertForbidden();
    });

    it('forbids SUPER ADMIN from entering this page', function (): void {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(UserRole::SUPER_ADMIN);

        actingAs($superAdmin)
            ->get(route('exponent.info-items.index'))
            ->assertForbidden();
    });

    it('forbids USER from entering this page', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        actingAs($user)
            ->get(route('exponent.info-items.index'))
            ->assertForbidden();
    });

    it('forbids guests from entering this page', function (): void {
        get(route('exponent.info-items.index'))
            ->assertRedirect(route('login'));
    });

    it('displays info items that belong to the exponent company on this page', function (): void {
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $otherCompany = Company::factory()->for($exhibition)->create();

        $exponent = User::factory()->create();
        $exponent->assignRole(UserRole::EXPONENT);
        $exponent->company()->associate($company)->save();

        $infoItems = InfoItem::factory(3)->for($exhibition)->create();
        InfoItem::factory(2)->for(Exhibition::factory()->create())->create();

        actingAs($exponent)
            ->get(route('exponent.info-items.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->component('exponent/InfoItems/Index')
                    ->has('infoItems', 3)
            );
    });
});
