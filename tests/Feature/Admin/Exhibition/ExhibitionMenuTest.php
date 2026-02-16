<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Exhibition;
use App\Models\User;

describe('Exhibition Menu Test', function (): void {

    it('shows the short initial menu for admins', function (): void {

        $user = User::factory()->create([
            'email' => 'admin@gmail.com',
            'password' => 'password',
        ]);

        $user->assignRole(UserRole::ADMIN);
        $exhibition = Exhibition::factory()->create();
        $exhibition->users()->attach($user);

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', '')
            ->fill('email', 'admin@gmail.com')
            ->fill('password', '')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('admin@gmail.com');

        $this->assertAuthenticated();

        $page->click('@collapse-menu-button')
            ->assertSeeLink('Главная')
            ->assertDontSeeLink('События программы')
            ->assertDontSeeLink('Люди')
            ->assertDontSeeLink('Компании')
            ->assertDontSeeLink('Работа с партнерами')
            ->assertSeeLink('Выставки');
    });

    it('shows the short initial menu for super-admins', function (): void {

        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);

        $user->assignRole(UserRole::SUPER_ADMIN);

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', '')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', '')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->click('@collapse-menu-button')
            ->assertSeeLink('Главная')
            ->assertDontSeeLink('События программы')
            ->assertDontSeeLink('Люди')
            ->assertDontSeeLink('Компании')
            ->assertDontSeeLink('Работа с партнерами')
            ->assertSeeLink('Выставки');
    });

    it('shows the full menu on exhibition-related pages', function (): void {

        $expos = Exhibition::factory(10)->create();

        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);

        $user->assignRole(UserRole::SUPER_ADMIN);

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', '')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', '')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate('/admin/exhibitions')
            ->click('@edit-expo-'.$expos[0]->id);

        $page->click('@collapse-menu-button')
            ->assertSeeLink('Главная')
            ->assertSeeLink('События программы')
            ->assertSeeLink('Люди')
            ->assertSeeLink('Компании')
            ->assertSeeLink('Работа с партнерами')
            ->assertSeeLink('Выставки');
    });

    it('it does not show the link to the exhibitions page if the admin does not have access any exhibtion', function (): void {

        $user = User::factory()->create([
            'email' => 'admin@gmail.com',
            'password' => 'password',
        ]);

        $user->assignRole(UserRole::ADMIN);

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', '')
            ->fill('email', 'admin@gmail.com')
            ->fill('password', '')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('admin@gmail.com');

        $this->assertAuthenticated();

        $page->click('@collapse-menu-button')
            ->assertSeeLink('Главная')
            ->assertDontSeeLink('События программы')
            ->assertDontSeeLink('Люди')
            ->assertDontSeeLink('Компании')
            ->assertDontSeeLink('Работа с партнерами')
            ->assertDontSeeLink('Выставки');
    });
});
