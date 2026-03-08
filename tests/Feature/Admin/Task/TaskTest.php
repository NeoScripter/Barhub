<?php

declare(strict_types=1);

use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Exhibition;
use App\Models\Task;
use App\Models\User;

use function PHPUnit\Framework\assertEquals;

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

    it('doesnt allow to create a task when the title is too long', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $route = "/admin/exhibitions/{$exhibition->id}/companies/{$company->id}/tasks";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee($company->public_name)
            ->click('@create-task')
            ->assertSee('Название')
            ->fill('title', generateTextWithChars(104))
            ->fill('description', generateTextWithChars(5004))
            ->fill('deadline', '2020-03-20T12:02')
            ->click('@submit-create-task')
            ->assertSee('Название задачи не должно превышать 100 символов');
    });

    it('doesnt allow to create a task when the description is too short', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $route = "/admin/exhibitions/{$exhibition->id}/companies/{$company->id}/tasks";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee($company->public_name)
            ->click('@create-task')
            ->assertSee('Название')
            ->fill('title', generateTextWithChars(50))
            ->fill('description', generateTextWithChars(3))
            ->fill('deadline', now()->addYear()->format('Y') . '-03-20T12:02')
            ->click('@submit-create-task')
            ->assertSee('Описание задачи должно содержать не менее 10 символов');
    });

    it('doesnt allow to create a task when the description is too long', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $route = "/admin/exhibitions/{$exhibition->id}/companies/{$company->id}/tasks";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee($company->public_name)
            ->click('@create-task')
            ->assertSee('Название')
            ->fill('title', generateTextWithChars(50))
            ->fill('description', generateTextWithChars(5004))
            ->fill('deadline', now()->addYear()->format('Y') . '-03-20T12:02')
            ->click('@submit-create-task')
            ->assertSee('Описание задачи не должно превышать 5000 символов');
    });

    it('doesnt allow to create a task when the deadline is in the past', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $route = "/admin/exhibitions/{$exhibition->id}/companies/{$company->id}/tasks";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee($company->public_name)
            ->click('@create-task')
            ->assertSee('Название')
            ->fill('title', generateTextWithChars(50))
            ->fill('description', generateTextWithChars(20))
            ->fill('deadline', '2020-03-20T12:02')
            ->click('@submit-create-task')
            ->assertSee('Срок выполнения должен быть в будущем');
    });

    it('allows to create a task with valid data', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $route = "/admin/exhibitions/{$exhibition->id}/companies/{$company->id}/tasks";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee($company->public_name)
            ->click('@create-task')
            ->assertSee('Название')
            ->fill('title', generateTextWithChars(50))
            ->fill('description', generateTextWithChars(20))
            ->fill('deadline', now()->addYear()->format('Y') . '-03-20T12:02')
            ->click('@submit-create-task')
            ->assertPathEndsWith($route);
    });

    it('doesnt allow to update a task when the title is too long', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $task = Task::factory()
            ->for($company)
            ->create(['title' => 'Zebra', 'deadline' => now()]);
        $route = "/admin/exhibitions/{$exhibition->id}/companies/{$company->id}/tasks";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee($company->public_name)
            ->assertSee($task->title)
            ->click('@edit-task-' . $task->id)
            ->assertSee('Название')
            ->clear('title')
            ->fill('title', generateTextWithChars(1103))
            ->click('@submit-update-task')
            ->assertSee('Название задачи не должно превышать 100 символов');
    });

    it('doesnt allow to update a task when the description is too short', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $task = Task::factory()
            ->for($company)
            ->create(['title' => 'Zebra', 'deadline' => now()]);
        $route = "/admin/exhibitions/{$exhibition->id}/companies/{$company->id}/tasks";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee($company->public_name)
            ->assertSee($task->title)
            ->click('@edit-task-' . $task->id)
            ->assertSee('Название')
            ->clear('description')
            ->fill('description', generateTextWithChars(3))
            ->click('@submit-update-task')
            ->assertSee('Описание задачи должно содержать не менее 10 символов');
    });

    it('doesnt allow to update a task when the description is too long', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $task = Task::factory()
            ->for($company)
            ->create(['title' => 'Zebra', 'deadline' => now()]);
        $route = "/admin/exhibitions/{$exhibition->id}/companies/{$company->id}/tasks";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee($company->public_name)
            ->assertSee($task->title)
            ->click('@edit-task-' . $task->id)
            ->assertSee('Название')
            ->clear('description')
            ->fill('description', generateTextWithChars(15003))
            ->click('@submit-update-task')
            ->assertSee('Описание задачи не должно превышать 5000 символов');
    });

    it('doesnt allow to update a task when the deadline is in the past', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $task = Task::factory()
            ->for($company)
            ->create(['title' => 'Zebra', 'deadline' => now()]);
        $route = "/admin/exhibitions/{$exhibition->id}/companies/{$company->id}/tasks";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee($company->public_name)
            ->assertSee($task->title)
            ->click('@edit-task-' . $task->id)
            ->assertSee('Название')
            ->clear('deadline')
            ->fill('deadline', '2020-03-20T12:02')
            ->click('@submit-update-task')
            ->assertSee('Срок выполнения должен быть в будущем');
    });

    it('allows to update a task with valid data', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $task = Task::factory()
            ->for($company)
            ->create(['title' => 'Zebra', 'deadline' => now()]);
        $route = "/admin/exhibitions/{$exhibition->id}/companies/{$company->id}/tasks";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $newTitle = 'new title of the task';
        $newDescription = 'new long description of the task';

        $page->navigate($route)
            ->assertSee($company->public_name)
            ->assertSee($task->title)
            ->click('@edit-task-' . $task->id)
            ->assertSee('Название')
            ->clear('title')
            ->fill('title', $newTitle)
            ->clear('description')
            ->fill('description', $newDescription)
            ->clear('deadline')
            ->fill('deadline', now()->addYear()->format('Y') . '-03-20T12:02')
            ->click('@submit-update-task');

        $task = $task->fresh();
        $this->assertEquals($task->title, $newTitle);
        $this->assertEquals($task->description, $newDescription);
    });

    it('allows to delete a task', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $task = Task::factory()
            ->for($company)
            ->create(['title' => 'Zebra', 'deadline' => now()]);
        $route = "/admin/exhibitions/{$exhibition->id}/companies/{$company->id}/tasks";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee($company->public_name)
            ->assertSee($task->title)
            ->click('@edit-task-' . $task->id)
            ->assertSee('Название')
            ->click('@delete-task')
            ->click('@delete-btn')
            ->assertPathEndsWith($route)
            ->assertDontSee($task->title);
    });

    it('sorts the tasks by all the criteria', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();

        $date1 = now();
        $date2 = now()->addDay();
        $date3 = now()->addDays(2);

        $tasks = Task::factory()
            ->for($company)
            ->createMany([
                ['title' => 'Alpha', 'deadline' => $date1],
                ['title' => 'Beta', 'deadline' => $date2],
                ['title' => 'Zebra', 'deadline' => $date3],
            ]);
        $route = "/admin/exhibitions/{$exhibition->id}/companies/{$company->id}/tasks";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        // Test sorting by title
        $page->navigate($route);
        $page->assertSee($tasks[0]->title);
        $page->click('Задача');
        $page->assertSeeIn('#tasks-table tr:first-child td:first-child', 'Zebra');
        $page->click('Задача');
        $page->assertSeeIn('#tasks-table tr:first-child td:first-child', 'Alpha');

        // Test sorting by deadline
        $page->click('Дедлайн');
        $page->assertSeeIn('#tasks-table tr:first-child td:nth-child(2)', $date3->translatedFormat('j M. Y г., H:i'));
        $page->assertSeeIn('#tasks-table tr:nth-child(3) td:nth-child(2)', $date1->translatedFormat('j M. Y г., H:i'));
        $page->click('Дедлайн');
        $page->assertSeeIn('#tasks-table tr:first-child td:nth-child(2)', $date1->translatedFormat('j M. Y г., H:i'));
        $page->assertSeeIn('#tasks-table tr:nth-child(3) td:nth-child(2)', $date3->translatedFormat('j M. Y г., H:i'));

        // Test sorting by status
        $tasks[0]->update(['status' => TaskStatus::COMPLETED]);
        $tasks[2]->update(['status' => TaskStatus::TO_BE_COMPLETED]);
        $tasks[1]->update(['status' => TaskStatus::DELAYED]);

        $page->navigate($route);
        $page->click('Статус');
        $page->assertSeeIn('#tasks-table tr:first-child td:nth-child(3)', TaskStatus::DELAYED->label());
        $page->assertSeeIn('#tasks-table tr:nth-child(3) td:nth-child(3)', TaskStatus::COMPLETED->label());
        $page->click('Статус');
        $page->assertSeeIn('#tasks-table tr:first-child td:nth-child(3)', TaskStatus::COMPLETED->label());
        $page->assertSeeIn('#tasks-table tr:nth-child(3) td:nth-child(3)', TaskStatus::DELAYED->label());
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
