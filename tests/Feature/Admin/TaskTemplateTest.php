<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Exhibition;
use App\Models\TaskTemplate;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\assertDatabaseCount;

describe('Admin Task Template Permission Test', function (): void {
    beforeEach(function (): void {
        $this->exhibition = Exhibition::factory()->create();
        $this->task = TaskTemplate::factory(['exhibition_id' => $this->exhibition->id])->create();
        $this->route = "/admin/task-templates";
        $this->payload = [
            'title' => 'new title',
            'description' => generateTextWithChars(50),
            'deadline' => now()->addYear()->format('Y') . '-03-20T12:02',
            'comment' => 'new comment',
        ];
    });

    it('allows admins with access to this exhibition to enter this page', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);
        $this->exhibition->users()->attach($admin->id);

        $this->actingAs($admin)
            ->get($this->route)
            ->assertOk();
    });

    it('forbids admins without access to this exhibition from entering this page', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        $this->actingAs($admin)
            ->get($this->route)
            ->assertForbidden();
    });

    it('forbids admins without access from creating a task', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        $this->actingAs($admin)
            ->post("{$this->route}", $this->payload)
            ->assertForbidden();

        $this->assertDatabaseMissing('task_templates', [
            'title' => $this->payload['title'],
        ]);
    });

    it('forbids admins without access from updating a task template', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        $this->actingAs($admin)
            ->put("{$this->route}/{$this->task->id}", $this->payload)
            ->assertForbidden();

        $this->assertDatabaseMissing('task_templates', [
            'title' => $this->payload['title'],
        ]);
    });

    it('forbids admins without access from deleting a task template', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        $this->actingAs($admin)
            ->delete("{$this->route}/{$this->task->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('task_templates', [
            'id' => $this->task->id,
        ]);
    });

    it('forbids exponents from entering this page', function (): void {
        $exponent = User::factory()->create();
        $exponent->assignRole(UserRole::EXPONENT);

        $this->actingAs($exponent)
            ->get($this->route)
            ->assertForbidden();
    });

    it('forbids exponents from creating a task template', function (): void {
        $exponent = User::factory()->create();
        $exponent->assignRole(UserRole::EXPONENT);

        $this->actingAs($exponent)
            ->post($this->route, $this->payload)
            ->assertForbidden();

        $this->assertDatabaseMissing('task_templates', [
            'title' => $this->payload['title'],
        ]);
    });

    it('forbids exponents from updating a task template', function (): void {
        $exponent = User::factory()->create();
        $exponent->assignRole(UserRole::EXPONENT);

        $this->actingAs($exponent)
            ->put("{$this->route}/{$this->task->id}", $this->payload)
            ->assertForbidden();

        $this->assertDatabaseMissing('task_templates', [
            'title' => $this->payload['title'],
        ]);
    });

    it('forbids exponents from deleting a task template', function (): void {
        $exponent = User::factory()->create();
        $exponent->assignRole(UserRole::EXPONENT);

        $this->actingAs($exponent)
            ->delete("{$this->route}/{$this->task->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('task_templates', [
            'id' => $this->task->id,
        ]);
    });

    it('forbids users from entering this page', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        $this->actingAs($user)
            ->get($this->route)
            ->assertForbidden();
    });

    it('forbids users from creating a task template', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        $this->actingAs($user)
            ->post($this->route, $this->payload)
            ->assertForbidden();

        $this->assertDatabaseMissing('task_templates', [
            'title' => $this->payload['title'],
        ]);
    });

    it('forbids users from updating a task template', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        $this->actingAs($user)
            ->put("{$this->route}/{$this->task->id}", $this->payload)
            ->assertForbidden();

        $this->assertDatabaseMissing('task_templates', [
            'title' => $this->payload['title'],
        ]);
    });

    it('forbids users from deleting a task template', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        $this->actingAs($user)
            ->delete("{$this->route}/{$this->task->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('task_templates', [
            'id' => $this->task->id,
        ]);
    });

    it('forbids unregistered users to enter this page', function (): void {
        $this->get($this->route)
            ->assertRedirect('/login');
    });

    it('forbids unregistered users from creating a task template', function (): void {
        $this->post($this->route, $this->payload)
            ->assertRedirect('/login');

        $this->assertDatabaseMissing('task_templates', [
            'title' => $this->payload['title'],
        ]);
    });

    it('forbids unregistered users from updating a task template', function (): void {
        $this->put("{$this->route}/{$this->task->id}", $this->payload)
            ->assertRedirect('/login');

        $this->assertDatabaseMissing('task_templates', [
            'title' => $this->payload['title'],
        ]);
    });

    it('forbids unregistered users from deleting a task template', function (): void {
        $this->delete("{$this->route}/{$this->task->id}")
            ->assertRedirect('/login');

        $this->assertDatabaseHas('task_templates', [
            'id' => $this->task->id,
        ]);
    });
});


describe('Admin Task Template Test', function (): void {
    it('creates a list of new tasks for this exhibition when a new exponent is created based on the current task templates', function (): void {
        assertDatabaseCount('task_templates', 0);

        $exhibition = Exhibition::factory()->create();
        TaskTemplate::factory(4)->for($exhibition)->create();

        assertDatabaseCount('task_templates', 5);
        assertDatabaseCount('tasks', 0);

        Company::factory()->for($exhibition)->create();

        assertDatabaseCount('tasks', 5);
    });

    it('creates a new task for this exhibition when a new task template is created', function (): void {
        assertDatabaseCount('task_templates', 0);
        assertDatabaseCount('tasks', 0);

        $exhibition = Exhibition::factory()->create();
        Company::factory()->for($exhibition)->create();
        TaskTemplate::factory(4)->for($exhibition)->create();

        assertDatabaseCount('task_templates', 5);
        assertDatabaseCount('tasks', 5);
    });

    it('has a default task template that requires each new exponent to fill in the information about their company', function (): void {
        assertDatabaseCount('task_templates', 0);

        $exhibitions = Exhibition::factory(3)->create();

        assertDatabaseCount('task_templates', $exhibitions->count());

        $exhibitions->each(function ($exhibition) {
            Company::factory()->for($exhibition)->create();
        });

        assertDatabaseCount('tasks', $exhibitions->count());
    });

    it('deletes a file in the storage after the model is deleted', function (): void {
        Storage::fake('local');

        $user = User::factory()->create([
            'email'    => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();

        $file = UploadedFile::fake()->create('document.pdf', 100);
        $filePath = Storage::put('task-template-files', $file);

        $task = TaskTemplate::factory()->for($exhibition)->create([
            'file_url'  => $filePath,
            'file_name' => 'document.pdf',
        ]);

        Storage::assertExists($filePath);

        $this->actingAs($user)
            ->delete("/admin/task-templates/{$task->id}")
            ->assertRedirect('/admin/task-templates');

        $this->assertDatabaseMissing('task_templates', ['id' => $task->id]);
        Storage::assertMissing($filePath);
    });

    it('displays the filename of the comment file and the comment comment in the task edit form when a task has a file', function (): void {
        Storage::fake('local');

        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $task = TaskTemplate::factory()->for($exhibition)->create([
            'comment' => 'new comment',
            'file_name' => 'document.pdf',
            'file_url' => Storage::fake('local')->put('task-template-files', UploadedFile::fake()->create('document.pdf', 100)),
        ]);
        $route = "/admin/task-templates";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee('Общие задачи')
            ->click("@edit-task-{$task->id}")
            ->assertSee('Редактировать общую задачу')
            ->assertSee('document.pdf')
            ->fill('comment', 'new comment 2')
            ->fill('deadline', now()->addYear()->format('Y') . '-03-20T12:02')
            ->submit()
            ->click("@edit-task-{$task->id}")
            ->assertSee('new comment 2');
    });

    it('successfully updates the task when a file is passed to the request', function (): void {
        Storage::fake('local');

        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $task = TaskTemplate::factory()->for($exhibition)->create();
        $route = "/admin/task-templates";

        $this->actingAs($user)
            ->get($route . "/{$task->id}/edit")
            ->assertInertia(
                fn(AssertableInertia $page): AssertableInertia => $page->component('admin/TaskTemplates/Edit')
            );

        $file = UploadedFile::fake()->create('document.pdf', 100);
        $payload = [
            'title' => 'new title',
            'description' => generateTextWithChars(50),
            'deadline' => now()->addYear()->format('Y') . '-03-20T12:02',
            'comment' => 'new comment',
            'file_url' => $file,
            'file_name' => 'document.pdf',
        ];

        $this->actingAs($user)
            ->patch($route . "/{$task->id}", $payload)
            ->assertRedirect($route);

        $this->assertDatabaseHas('task_templates', [
            'id' => $task->id,
            'title' => $payload['title'],
            'description' => $payload['description'],
            'file_name' => $payload['file_name'],
        ]);

        Storage::assertExists($task->file_url);
    });

    it('successfully creates a task with a file when the comment and file are passed to the request', function (): void {
        Storage::fake('local');

        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $route = "/admin/task-templates";

        $file = UploadedFile::fake()->create('document.pdf', 100);
        $payload = [
            'title' => 'new title',
            'description' => generateTextWithChars(50),
            'deadline' => now()->addYear()->format('Y') . '-03-20T12:02',
            'comment' => 'new comment',
            'file_url' => $file,
            'file_name' => 'document.pdf',
        ];

        $this->actingAs($user)
            ->post($route, $payload)
            ->assertRedirect($route);

        $task = TaskTemplate::query()->where('title', $payload['title'])->first();
        expect($task)->not->toBeNull();
        expect($task->comment)->toBe($payload['comment']);

        Storage::assertExists($task->file_url);
    });

    it('successfully creates a task without a file and comment when a file is passed to the request without a comment', function (): void {
        Storage::fake('local');

        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $route = "/admin/task-templates";

        $file = UploadedFile::fake()->create('document.pdf', 100);
        $payload = [
            'title' => 'new title',
            'description' => generateTextWithChars(50),
            'deadline' => now()->addYear()->format('Y') . '-03-20T12:02',
            'file_url' => $file,
            'file_name' => 'file name',
        ];

        $this->actingAs($user)
            ->post($route, $payload)
            ->assertRedirect($route);

        $task = TaskTemplate::query()->where('title', $payload['title'])->first();
        expect($task)->not->toBeNull();

        $this->assertDatabaseCount('task_files', 0);
    });

    it('successfully updates the task when the task does not have a comment and a comment is passed to the request', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);

        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $task = TaskTemplate::factory()->for($exhibition)->create();
        $route = "/admin/task-templates";

        $this->actingAs($user)
            ->get($route . "/{$task->id}/edit")
            ->assertInertia(
                fn(AssertableInertia $page): AssertableInertia => $page->component('admin/TaskTemplates/Edit')
            );

        $payload = [
            'title' => 'new title',
            'description' => generateTextWithChars(50),
            'deadline' => now()->addYear()->format('Y') . '-03-20T12:02',
            'comment' => 'new comment',
        ];

        $this->actingAs($user)
            ->put($route . "/{$task->id}", $payload)
            ->assertRedirect($route);

        $this->assertDatabaseHas('task_templates', [
            'id' => $task->id,
            'title' => $payload['title'],
            'description' => $payload['description'],
            'comment' => $payload['comment'],
        ]);
    });

    it('successfully updates the task when the task has a comment and no comment is passed to the request', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);

        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $task = TaskTemplate::factory(['comment' => 'new comment'])->for($exhibition)->create();
        $route = "/admin/task-templates";

        $this->actingAs($user)
            ->get($route . "/{$task->id}/edit")
            ->assertInertia(
                fn(AssertableInertia $page): AssertableInertia => $page->component('admin/TaskTemplates/Edit')
            );

        $payload = [
            'title' => generateTextWithChars(50),
            'description' => generateTextWithChars(50),
            'deadline' => now()->addYear()->format('Y') . '-03-20T12:02',
        ];

        $this->actingAs($user)
            ->put($route . "/{$task->id}", $payload)
            ->assertRedirect($route);

        $this->assertDatabaseHas('task_templates', [
            'id' => $task->id,
            'title' => $payload['title'],
        ]);
    });

    it('renders the task index page', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $tasks = TaskTemplate::factory(3)->for($exhibition)->create();
        $route = "/admin/task-templates";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route);
        $page->assertSee('Общие задачи');
        $page->assertSee($tasks[0]->title);
    });

    it('doesnt allow to create a task when the title is too long', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $route = "/admin/task-templates";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee('Общие задачи')
            ->click('@create-task')
            ->assertSee('Название')
            ->fill('title', generateTextWithChars(104))
            ->fill('description', generateTextWithChars(5004))
            ->fill('deadline', '2020-03-20T12:02')
            ->submit()
            ->assertSee('Название задачи не должно превышать 100 символов');
    });

    it('doesnt allow to create a task when the description is too short', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $route = "/admin/task-templates";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee('Общие задачи')
            ->click('@create-task')
            ->assertSee('Название')
            ->fill('title', generateTextWithChars(50))
            ->fill('description', generateTextWithChars(3))
            ->fill('deadline', now()->addYear()->format('Y') . '-03-20T12:02')
            ->submit()
            ->assertSee('Описание задачи должно содержать не менее 10 символов');
    });

    it('doesnt allow to create a task when the description is too long', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $route = "/admin/task-templates";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee('Общие задачи')
            ->click('@create-task')
            ->assertSee('Название')
            ->fill('title', generateTextWithChars(50))
            ->fill('description', generateTextWithChars(5004))
            ->fill('deadline', now()->addYear()->format('Y') . '-03-20T12:02')
            ->submit()
            ->assertSee('Описание задачи не должно превышать 5000 символов');
    });

    it('doesnt allow to create a task when the comment is too long', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $route = "/admin/task-templates";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee('Общие задачи')
            ->click('@create-task')
            ->assertSee('Название')
            ->fill('title', generateTextWithChars(50))
            ->fill('description', generateTextWithChars(100))
            ->fill('comment', generateTextWithChars(2005))
            ->fill('deadline', now()->addYear()->format('Y') . '-03-20T12:02')
            ->submit()
            ->assertSee('Комментарий не должен превышать 2000 символов');
    });

    it('doesnt allow to create a task when the deadline is in the past', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $route = "/admin/task-templates";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee('Общие задачи')
            ->click('@create-task')
            ->assertSee('Название')
            ->fill('title', generateTextWithChars(50))
            ->fill('description', generateTextWithChars(20))
            ->fill('deadline', '2020-03-20T12:02')
            ->submit()
            ->assertSee('Срок выполнения должен быть в будущем');
    });

    it('allows to create a task with valid data', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $route = "/admin/task-templates";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee('Общие задачи')
            ->click('@create-task')
            ->assertSee('Название')
            ->fill('title', generateTextWithChars(50))
            ->fill('description', generateTextWithChars(20))
            ->fill('deadline', now()->addYear()->format('Y') . '-03-20T12:02')
            ->fill('comment', generateTextWithChars(100))
            ->submit()
            ->assertPathEndsWith($route);
    });

    it('doesnt allow to update a task when the title is too long', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $task = TaskTemplate::factory()->for($exhibition)
            ->create(['title' => 'Zebra', 'deadline' => now()]);
        $route = "/admin/task-templates";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee('Общие задачи')
            ->assertSee($task->title)
            ->click('@edit-task-' . $task->id)
            ->assertSee('Название')
            ->clear('title')
            ->fill('title', generateTextWithChars(1103))
            ->submit()
            ->assertSee('Название задачи не должно превышать 100 символов');
    });

    it('doesnt allow to update a task when the description is too short', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $task = TaskTemplate::factory()->for($exhibition)
            ->create(['title' => 'Zebra', 'deadline' => now()]);
        $route = "/admin/task-templates";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee('Общие задачи')
            ->assertSee($task->title)
            ->click('@edit-task-' . $task->id)
            ->assertSee('Название')
            ->clear('description')
            ->fill('description', generateTextWithChars(3))
            ->submit()
            ->assertSee('Описание задачи должно содержать не менее 10 символов');
    });

    it('doesnt allow to update a task when the description is too long', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $task = TaskTemplate::factory()->for($exhibition)
            ->create(['title' => 'Zebra', 'deadline' => now()]);
        $route = "/admin/task-templates";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee('Общие задачи')
            ->assertSee($task->title)
            ->click('@edit-task-' . $task->id)
            ->assertSee('Название')
            ->clear('description')
            ->fill('description', generateTextWithChars(15003))
            ->submit()
            ->assertSee('Описание задачи не должно превышать 5000 символов');
    });

    it('doesnt allow to update a task when the comment is too long', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $task = TaskTemplate::factory()->for($exhibition)
            ->create(['title' => 'Zebra', 'deadline' => now()]);
        $route = "/admin/task-templates";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee('Общие задачи')
            ->assertSee($task->title)
            ->click('@edit-task-' . $task->id)
            ->assertSee('Название')
            ->clear('comment')
            ->fill('comment', generateTextWithChars(2005))
            ->submit()
            ->assertSee('Комментарий не должен превышать 2000 символов');
    });

    it('doesnt allow to update a task when the deadline is in the past', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $task = TaskTemplate::factory()->for($exhibition)
            ->create(['title' => 'Zebra', 'deadline' => now()]);
        $route = "/admin/task-templates";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee('Общие задачи')
            ->assertSee($task->title)
            ->click('@edit-task-' . $task->id)
            ->assertSee('Название')
            ->clear('deadline')
            ->fill('deadline', '2020-03-20T12:02')
            ->submit()
            ->assertSee('Срок выполнения должен быть в будущем');
    });

    it('allows to update a task with valid data', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $task = TaskTemplate::factory()->for($exhibition)

            ->create(['title' => 'Zebra', 'deadline' => now()]);
        $route = "/admin/task-templates";

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
            ->assertSee('Общие задачи')
            ->assertSee($task->title)
            ->click('@edit-task-' . $task->id)
            ->assertSee('Название')
            ->clear('title')
            ->fill('title', $newTitle)
            ->clear('description')
            ->fill('description', $newDescription)
            ->clear('comment')
            ->fill('comment', generateTextWithChars(100))
            ->clear('deadline')
            ->fill('deadline', now()->addYear()->format('Y') . '-03-20T12:02')
            ->submit();

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
        $task = TaskTemplate::factory()->for($exhibition)

            ->create(['title' => 'Zebra', 'deadline' => now()]);
        $route = "/admin/task-templates";

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee('Общие задачи')
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
        $exhibition->taskTemplates()->each(fn($template) => $template->delete());

        $date1 = now();
        $date2 = now()->addDay();
        $date3 = now()->addDays(2);
        $date1 = now();
        Date::setLocale('ru');

        $tasks = TaskTemplate::factory()->for($exhibition)

            ->createMany([
                ['title' => 'Alpha', 'deadline' => $date1],
                ['title' => 'Beta', 'deadline' => $date2],
                ['title' => 'Zebra', 'deadline' => $date3],
            ]);
        $route = "/admin/task-templates";

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
        $page->click('Заголовок');
        $page->assertSeeIn('#templates-table tr:first-child td:first-child', 'Zebra');
        $page->click('Заголовок');
        $page->assertSeeIn('#templates-table tr:first-child td:first-child', 'Alpha');

        // Test sorting by deadline
        $page->click('Дедлайн');
        $page->assertSeeIn('#templates-table tr:first-child td:nth-child(2)', $date3->translatedFormat('j M. Y'));
        $page->assertSeeIn('#templates-table tr:nth-child(3) td:nth-child(2)', $date1->translatedFormat('j M. Y'));
        $page->click('Дедлайн');
        $page->assertSeeIn('#templates-table tr:first-child td:nth-child(2)', $date1->translatedFormat('j M. Y'));
        $page->assertSeeIn('#templates-table tr:nth-child(3) td:nth-child(2)', $date3->translatedFormat('j M. Y'));
    });
});
