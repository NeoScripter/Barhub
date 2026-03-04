<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Exhibition;
use App\Models\User;

describe('Exponent Assignment Test', function (): void {

    it('allows super-admin to assign and revoke exponent status', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);

        $exponents = User::factory(10)->create();

        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();

        $route = "/admin/exhibitions/{$exhibition->id}/companies/{$company->id}/exponents";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route);

        $page->assertSee($company->public_name);

        $page->click('@select-exponent');

        $page->assertCount('#exponent-list li', 0);
        $page->assertSee('Пользователи');

        $page->press('Выбрать пользователя');
        $page->click('@select-content');
        $page->click('@add-exponent');

        $page->assertCount('#exponent-list li', 1);
        $page->click('Удалить');
        $page->assertSee('Удалить экспонента?');
        $page->click('@delete-btn');

        $page->assertCount('#exponent-list li', 0);
    })->group('browser');
});
