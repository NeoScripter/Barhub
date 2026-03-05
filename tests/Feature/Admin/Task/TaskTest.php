<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Exhibition;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;

describe('Admin Task Test', function (): void {

    it('renders the task index page', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $tasks = Task::factory(3)->for($company)->create();
        $route = "/admin/exhibitions/{$exhibition->id}/companies/{$company->id}/tasks";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route);
        $page->assertSee($company->public_name);
        $page->assertSee($tasks[0]->title);
    });

    it('allows only super admin and admin with exhibition access to see the tasks index page', function (): void {
        $adminWithAccess = User::factory()->create([
            'email' => 'admin1@gmail.com',
            'password' => 'password',
        ]);
        $adminWithoutAccess = User::factory()->create([
            'email' => 'admin2@gmail.com',
            'password' => 'password',
        ]);
        $exponent = User::factory()->create([
            'email' => 'exponent@gmail.com',
            'password' => 'password',
        ]);
        $adminWithAccess->assignRole(UserRole::ADMIN);
        $adminWithoutAccess->assignRole(UserRole::ADMIN);
        $exponent->assignRole(UserRole::EXPONENT);
        $exhibitionWithCompany = Exhibition::factory()->create();
        $exhibitionWithoutCompany = Exhibition::factory()->create();
        $exhibitionWithCompany->users()->attach($adminWithAccess->id);
        $exhibitionWithoutCompany->users()->attach($adminWithoutAccess->id);
        $company = Company::factory()->for($exhibitionWithCompany)->create();
        $tasks = Task::factory(3)->for($company)->create();
        $route = "/admin/exhibitions/{$exhibitionWithCompany->id}/companies/{$company->id}/tasks";

        visit($route)->assertSee('Вход в аккаунт');

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'admin1@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('admin1@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route);
        $page->assertSee($company->public_name);
        $page->assertSee($tasks[0]->title);

        $page->click('@logout-dropdown');
        $page->assertSee("Выйти");
        $page->click('@logout-button');

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'admin2@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('admin2@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)->assertSee('Unauthorized');
        $page->navigate('/admin/dashboard');
        $page->click('@logout-dropdown');
        $page->assertSee("Выйти");
        $page->click('@logout-button');

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'exponent@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('exponent@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)->assertSee('Unauthorized');
    });

    // it('allows super-admin to create and delete tags', function (): void {

    //     $page->assertSee('Управление тегами');

    //     $page->click('@edit-tags');

    //     $page->assertSee('Добавить тег');

    //     $page->press('Добавить');
    //     $page->assertCount('#tag-list li', 3);

    //     $newTagName = 'Семинар';
    //     $page->type('tagname', $newTagName);

    //     // Submit the form
    //     $page->click('Добавить');

    //     $page->assertSee($newTagName);
    //     $page->assertCount('#tag-list li', 4);
    //     $page->click('@delete tag ' . $newTagName);

    //     $page->assertCount('#tag-list li', 3);
    //     $page->assertDontSee($newTagName);
    // })->group('browser');
});
