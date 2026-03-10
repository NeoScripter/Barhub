<?php

declare(strict_types=1);

use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Exhibition;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

describe('Admin Partner Browser Tests', function (): void {

    it('renders the partners index page', function () {});
    it('renders the partners edit page', function () {});
    it('successfully updates the status of a task', function () {});
    it('does not display complete tasks on the index page', function () {});
    it('displays the correct number of tasks in each category', function () {});
    it('displays only the tasks that belong to this exhibition', function () {});
})->group('browser')->todo();

describe('Admin Partner Feature Tests', function (): void {

    beforeEach(function (): void {
        $this->superAdmin = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $this->superAdmin->assignRole(UserRole::SUPER_ADMIN);
        $this->exhibition = Exhibition::factory()->create();
        $this->route = "/admin/exhibitions/{$this->exhibition->id}/all-tasks";
    });

    it('sorts tasks by title in desc order', function () {
        $company = Company::factory()->for($this->exhibition)->create();
        Task::factory()
            ->count(3)
            ->for($company)
            ->sequence(
                ['title' => 'Zebra'],
                ['title' => 'Alpha'],
                ['title' => 'Beta'],
            )
            ->create();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.all-tasks.index', [
                'exhibition' => $this->exhibition,
                'sort' => '-title',
            ]));

        $response->assertOk();

        $tasks = $response->viewData('page')['props']['tasks']['data'];
        expect($tasks[0]['title'])->toBe('Zebra')
            ->and($tasks[1]['title'])->toBe('Beta')
            ->and($tasks[2]['title'])->toBe('Alpha');
    });

    it('sorts tasks by title in asc order', function () {
        $company = Company::factory()->for($this->exhibition)->create();
        Task::factory()
            ->count(3)
            ->for($company)
            ->sequence(
                ['title' => 'Zebra'],
                ['title' => 'Alpha'],
                ['title' => 'Beta'],
            )
            ->create();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.all-tasks.index', [
                'exhibition' => $this->exhibition,
                'sort' => 'title',
            ]));

        $response->assertOk();

        $tasks = $response->viewData('page')['props']['tasks']['data'];
        expect($tasks[0]['title'])->toBe('Alpha')
            ->and($tasks[1]['title'])->toBe('Beta')
            ->and($tasks[2]['title'])->toBe('Zebra');
    });

    it('sorts tasks by company name in desc order', function () {
        Company::factory()->for($this->exhibition)
            ->has(Task::factory())
            ->count(3)
            ->sequence(
                ['public_name' => 'Zebra'],
                ['public_name' => 'Alpha'],
                ['public_name' => 'Beta'],
            )
            ->create();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.all-tasks.index', [
                'exhibition' => $this->exhibition,
                'sort' => '-company.public_name',
            ]));

        $response->assertOk();

        $tasks = $response->viewData('page')['props']['tasks']['data'];
        expect($tasks[0]['company']['public_name'])->toBe('Zebra')
            ->and($tasks[1]['company']['public_name'])->toBe('Beta')
            ->and($tasks[2]['company']['public_name'])->toBe('Alpha');
    });

    it('sorts tasks by company name in asc order', function () {
        Company::factory()->for($this->exhibition)
            ->has(Task::factory())
            ->count(3)
            ->sequence(
                ['public_name' => 'Zebra'],
                ['public_name' => 'Alpha'],
                ['public_name' => 'Beta'],
            )
            ->create();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.all-tasks.index', [
                'exhibition' => $this->exhibition,
                'sort' => 'company.public_name',
            ]));

        $response->assertOk();

        $tasks = $response->viewData('page')['props']['tasks']['data'];
        expect($tasks[0]['company']['public_name'])->toBe('Alpha')
            ->and($tasks[1]['company']['public_name'])->toBe('Beta')
            ->and($tasks[2]['company']['public_name'])->toBe('Zebra');
    });

    it('sorts tasks by deadline in asc and desc order', function () {
        $company = Company::factory()->for($this->exhibition)->create();
        Task::factory()
            ->count(3)
            ->for($company)
            ->sequence(
                ['deadline' => now()->addDays(10)],
                ['deadline' => now()->addDays(1)],
                ['deadline' => now()->addDays(5)],
            )
            ->create();

        $tasks = $this->actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.all-tasks.index', [
                'exhibition' => $this->exhibition,
                'sort'       => 'deadline',
            ]))
            ->assertOk()
            ->viewData('page')['props']['tasks']['data'];

        expect($tasks[0]['deadline'])->toBeLessThan($tasks[1]['deadline'])
            ->and($tasks[1]['deadline'])->toBeLessThan($tasks[2]['deadline']);

        $tasks = $this->actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.all-tasks.index', [
                'exhibition' => $this->exhibition,
                'sort'       => '-deadline',
            ]))
            ->assertOk()
            ->viewData('page')['props']['tasks']['data'];

        expect($tasks[0]['deadline'])->toBeGreaterThan($tasks[1]['deadline'])
            ->and($tasks[1]['deadline'])->toBeGreaterThan($tasks[2]['deadline']);
    });

    it('sorts tasks by status in asc and desc order', function () {
        $company = Company::factory()->for($this->exhibition)->create();
        Task::factory()
            ->count(3)
            ->for($company)
            ->sequence(
                ['status' => TaskStatus::COMPLETED],
                ['status' => TaskStatus::DELAYED],
                ['status' => TaskStatus::TO_BE_COMPLETED],
            )
            ->create();

        $tasks = $this->actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.all-tasks.index', [
                'exhibition' => $this->exhibition,
                'sort'       => 'status',
            ]))
            ->assertOk()
            ->viewData('page')['props']['tasks']['data'];

        $statuses = array_column($tasks, 'status');
        expect($statuses)->toBe(collect($statuses)->sort()->values()->all());

        $tasks = $this->actingAs($this->superAdmin)
            ->get(route('admin.exhibitions.all-tasks.index', [
                'exhibition' => $this->exhibition,
                'sort'       => '-status',
            ]))
            ->assertOk()
            ->viewData('page')['props']['tasks']['data'];

        $statuses = array_column($tasks, 'status');
        expect($statuses)->toBe(collect($statuses)->sortDesc()->values()->all());
    });

    it('displays the comments on the edit page from oldest to newest', function () {
        $company  = Company::factory()->for($this->exhibition)->create();
        $task     = Task::factory()->for($company)->create();
        $route    = "/admin/exhibitions/{$this->exhibition->id}/all-tasks";

        TaskComment::factory()
            ->count(3)
            ->for($task)
            ->for($this->superAdmin, 'user')
            ->sequence(
                ['content' => 'oldest comment',  'created_at' => now()->subDays(2)],
                ['content' => 'middle comment',  'created_at' => now()->subDay()],
                ['content' => 'newest comment',  'created_at' => now()],
            )
            ->create();

        $comments = $this->actingAs($this->superAdmin)
            ->get("$route/$task->id/edit")
            ->assertOk()
            ->assertInertia(
                fn(AssertableInertia $page) => $page
                    ->component('admin/Partners/Edit')
                    ->has('task.comments', 3)
            )
            ->viewData('page')['props']['task']['comments'];

        expect($comments[0]['content'])->toBe('oldest comment')
            ->and($comments[1]['content'])->toBe('middle comment')
            ->and($comments[2]['content'])->toBe('newest comment');
    });

    it('allows super admin to enter this page', function () {
        $this->actingAs($this->superAdmin)
            ->get($this->route)
            ->assertOk();
    });

    it('allows super admin to update the status of a task', function () {
        $company = Company::factory()->for($this->exhibition)->create();
        $task = Task::factory(['status' => TaskStatus::IMCOMPLETE->value])
            ->for($company)
            ->create();

        $this->actingAs($this->superAdmin)
            ->patch($this->route . "/$task->id", ['is_accepted' => true])
            ->assertRedirect($this->route);

        $this->assertDatabaseHas('tasks', [
            'id'          => $task->id,
            'status' => TaskStatus::COMPLETED->value
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

    it('forbids admins without access from updating the status of a task', function () {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);
        $company = Company::factory()->for($this->exhibition)->create();
        $task = Task::factory(['status' => TaskStatus::IMCOMPLETE->value])
            ->for($company)
            ->create();

        $this->actingAs($admin)
            ->patch($this->route . "/$task->id", ['is_accepted' => true])
            ->assertForbidden();

        $this->assertDatabaseHas('tasks', [
            'id'          => $task->id,
            'status' => TaskStatus::IMCOMPLETE->value
        ]);
    });

    it('forbids exponents from entering this page', function () {
        $exponent = User::factory()->create();
        $exponent->assignRole(UserRole::EXPONENT);

        $this->actingAs($exponent)
            ->get($this->route)
            ->assertForbidden();
    });

    it('forbids exponents from updating the status of a task', function () {
        $exponent = User::factory()->create();
        $exponent->assignRole(UserRole::EXPONENT);
        $company = Company::factory()->for($this->exhibition)->create();
        $task = Task::factory(['status' => TaskStatus::IMCOMPLETE->value])
            ->for($company)
            ->create();

        $this->actingAs($exponent)
            ->patch($this->route . "/$task->id", ['is_accepted' => true])
            ->assertForbidden();

        $this->assertDatabaseHas('tasks', [
            'id'          => $task->id,
            'status' => TaskStatus::IMCOMPLETE->value
        ]);
    });

    it('forbids users from entering this page', function () {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        $this->actingAs($user)
            ->get($this->route)
            ->assertForbidden();
    });

    it('forbids users from updating the status of a task', function () {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);
        $company = Company::factory()->for($this->exhibition)->create();
        $task = Task::factory(['status' => TaskStatus::IMCOMPLETE->value])
            ->for($company)
            ->create();

        $this->actingAs($user)
            ->patch($this->route . "/$task->id", ['is_accepted' => true])
            ->assertForbidden();

        $this->assertDatabaseHas('tasks', [
            'id'          => $task->id,
            'status' => TaskStatus::IMCOMPLETE->value
        ]);
    });

    it('forbids unregistered users to enter this page', function () {
        $this->get($this->route)
            ->assertRedirect('/login');
    });

    it('forbids unregistered users to update the status of a task', function () {
        $company = Company::factory()->for($this->exhibition)->create();
        $task = Task::factory(['status' => TaskStatus::IMCOMPLETE->value])
            ->for($company)
            ->create();

        $this->patch($this->route . "/$task->id", ['is_accepted' => true])
            ->assertRedirect('/login');

        $this->assertDatabaseHas('tasks', [
            'id'          => $task->id,
            'status' => TaskStatus::IMCOMPLETE->value
        ]);
    });
})->group('feature');;
