<?php

declare(strict_types=1);

use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Exhibition;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;

describe('Admin Task Test', function (): void {

    it('deletes all the files in the storage after the model is deleted', function (): void {
        Storage::fake('public');

        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $task = Task::factory()->for($company)->create();
        $route = "/admin/companies/{$company->id}/tasks";

        $comment = $task->comments()->create([
            'content' => 'new content',
            'user_id' => $user->id,
        ]);

        $comment->file()->create([
            'name' => 'document.pdf',
            'url' => Storage::fake('public')->put('task-files', UploadedFile::fake()->create('document.pdf', 100)),
        ]);

        $url = $comment->file->url;

        $this->actingAs($user)
            ->delete($route . "/{$task->id}")
            ->assertRedirect($route);

        $this->assertDatabaseMissing('task_comments', ['id' => $comment->id]);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
        Storage::assertMissing($url);
    });

    it('displays the newest comment for the current user when editing the task', function (): void {

        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $task = Task::factory()->for($company)->create();
        $route = "/admin/companies/{$company->id}/tasks/{$task->id}/edit";

        $task->comments()->createMany([[
            'content' => 'comment one name',
            'user_id' => $user->id,
            'created_at' => now()->subMonths(2),
        ], [
            'content' => 'comment two name',
            'user_id' => $user->id,
            'created_at' => now()->subMonths(1),
        ], [
            'content' => 'comment three name',
            'user_id' => $user->id,
            'created_at' => now(),
        ]]);

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee('Редактировать задачу')
            ->assertDontSee('comment one name')
            ->assertDontSee('comment two name')
            ->assertSee('comment three name');
    });

    it('displays the filename of the comment file and the comment content in the task edit form when a task has a file', function (): void {
        Storage::fake('local');

        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $task = Task::factory()->for($company)->create();
        $route = "/admin/companies/{$company->id}/tasks";

        $comment = $task->comments()->create([
            'content' => 'new content',
            'user_id' => $user->id,
        ]);

        $comment->file()->create([
            'name' => 'document.pdf',
            'url' => Storage::fake('local')->put('task-files', UploadedFile::fake()->create('document.pdf', 100)),
        ]);

        $page = visit('/login');

        $page->assertSee('Вход в аккаунт')
            ->fill('email', 'super-admin@gmail.com')
            ->fill('password', 'password')
            ->click('@login-button')
            ->assertSee('super-admin@gmail.com');

        $this->assertAuthenticated();

        $page->navigate($route)
            ->assertSee($company->public_name)
            ->click("@edit-task-{$task->id}")
            ->assertSee('Редактировать задачу')
            ->assertSee('document.pdf')
            ->fill('comment', 'new content 2')
            ->fill('deadline', now()->addYear()->format('Y') . '-03-20T12:02')
            ->submit()
            ->click("@edit-task-{$task->id}")
            ->assertSee('new content 2');
    });

    it('successfully updates the task when a file is passed to the request', function (): void {
        Storage::fake('local');

        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $task = Task::factory()->for($company)->create();
        $route = "/admin/companies/{$company->id}/tasks";

        $this->actingAs($user)
            ->get($route . "/{$task->id}/edit")
            ->assertInertia(
                fn(AssertableInertia $page): AssertableInertia => $page->component('admin/Tasks/Edit')
            );

        $file = UploadedFile::fake()->create('document.pdf', 100);
        $payload = [
            'title' => 'new title',
            'description' => generateTextWithChars(50),
            'deadline' => now()->addYear()->format('Y') . '-03-20T12:02',
            'comment' => 'new comment',
            'file' => $file,
            'file_name' => 'document.pdf',
        ];

        $this->actingAs($user)
            ->put($route . "/{$task->id}", $payload)
            ->assertRedirect($route);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => $payload['title'],
            'description' => $payload['description'],
        ]);

        $comment = TaskComment::query()->where('content', $payload['comment'])->first();
        expect($comment)->not->toBeNull();

        $this->assertDatabaseHas('task_files', [
            'task_comment_id' => $comment->id,
            'name' => $payload['file_name'],
        ]);

        Storage::assertExists($comment->file->url);
    });

    it('successfully creates a task with a file when the comment and file are passed to the request', function (): void {
        Storage::fake('local');

        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $route = "/admin/companies/{$company->id}/tasks";

        $file = UploadedFile::fake()->create('document.pdf', 100);
        $payload = [
            'title' => 'new title',
            'description' => generateTextWithChars(50),
            'deadline' => now()->addYear()->format('Y') . '-03-20T12:02',
            'comment' => 'new comment',
            'file' => $file,
            'file_name' => 'document.pdf',
        ];

        $this->actingAs($user)
            ->post($route, $payload)
            ->assertRedirect($route);

        $task = Task::query()->where('title', $payload['title'])->first();
        expect($task)->not->toBeNull();

        $this->assertDatabaseHas('task_comments', [
            'task_id' => $task->id,
            'content' => $payload['comment'],
        ]);

        $comment = $task->comments()->where('content', $payload['comment'])->first();

        $this->assertDatabaseHas('task_files', [
            'task_comment_id' => $comment->id,
            'name' => $payload['file_name'],
        ]);

        Storage::assertExists($comment->file->url);
    });

    it('successfully creates a task without a file and comment when a file is passed to the request without a comment', function (): void {
        Storage::fake('local');

        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);
        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $route = "/admin/companies/{$company->id}/tasks";

        $file = UploadedFile::fake()->create('document.pdf', 100);
        $payload = [
            'title' => 'new title',
            'description' => generateTextWithChars(50),
            'deadline' => now()->addYear()->format('Y') . '-03-20T12:02',
            'file' => $file,
            'file_name' => 'file name',
        ];

        $this->actingAs($user)
            ->post($route, $payload)
            ->assertRedirect($route);

        $task = Task::query()->where('title', $payload['title'])->first();
        expect($task)->not->toBeNull();

        $this->assertDatabaseCount('task_comments', 0);
        $this->assertDatabaseCount('task_files', 0);
    });

    it('successfully updates the task when the task does not have a comment and a comment is passed to the request', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);

        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $task = Task::factory()->for($company)->create();
        $route = "/admin/companies/{$company->id}/tasks";

        $this->actingAs($user)
            ->get($route . "/{$task->id}/edit")
            ->assertInertia(
                fn(AssertableInertia $page): AssertableInertia => $page->component('admin/Tasks/Edit')
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

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => $payload['title'],
            'description' => $payload['description'],
        ]);

        $this->assertDatabaseHas('task_comments', [
            'content' => $payload['comment'],
        ]);
    });

    it('successfully updates the task when the task has a comment and no comment is passed to the request', function (): void {
        $user = User::factory()->create([
            'email' => 'super-admin@gmail.com',
            'password' => 'password',
        ]);

        $user->assignRole(UserRole::SUPER_ADMIN);
        $exhibition = Exhibition::factory()->create();
        $company = Company::factory()->for($exhibition)->create();
        $task = Task::factory()->for($company)->create();
        TaskComment::factory()->for($task)->create();
        $route = "/admin/companies/{$company->id}/tasks";

        $this->actingAs($user)
            ->get($route . "/{$task->id}/edit")
            ->assertInertia(
                fn(AssertableInertia $page): AssertableInertia => $page->component('admin/Tasks/Edit')
            );

        $payload = [
            'title' => generateTextWithChars(50),
            'description' => generateTextWithChars(50),
            'deadline' => now()->addYear()->format('Y') . '-03-20T12:02',
        ];

        $this->actingAs($user)
            ->put($route . "/{$task->id}", $payload)
            ->assertRedirect($route);

        $this->assertDatabaseHas('tasks', [
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
        $company = Company::factory()->for($exhibition)->create();
        $tasks = Task::factory(3)->for($company)->create();
        $route = "/admin/companies/{$company->id}/tasks";

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
        $route = "/admin/companies/{$company->id}/tasks";

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
        $company = Company::factory()->for($exhibition)->create();
        $route = "/admin/companies/{$company->id}/tasks";

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
        $company = Company::factory()->for($exhibition)->create();
        $route = "/admin/companies/{$company->id}/tasks";

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
        $company = Company::factory()->for($exhibition)->create();
        $route = "/admin/companies/{$company->id}/tasks";

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
        $company = Company::factory()->for($exhibition)->create();
        $route = "/admin/companies/{$company->id}/tasks";

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
        $company = Company::factory()->for($exhibition)->create();
        $route = "/admin/companies/{$company->id}/tasks";

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
        $company = Company::factory()->for($exhibition)->create();
        $task = Task::factory()
            ->for($company)
            ->create(['title' => 'Zebra', 'deadline' => now()]);
        $route = "/admin/companies/{$company->id}/tasks";

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
        $company = Company::factory()->for($exhibition)->create();
        $task = Task::factory()
            ->for($company)
            ->create(['title' => 'Zebra', 'deadline' => now()]);
        $route = "/admin/companies/{$company->id}/tasks";

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
        $company = Company::factory()->for($exhibition)->create();
        $task = Task::factory()
            ->for($company)
            ->create(['title' => 'Zebra', 'deadline' => now()]);
        $route = "/admin/companies/{$company->id}/tasks";

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
        $company = Company::factory()->for($exhibition)->create();
        $task = Task::factory()
            ->for($company)
            ->create(['title' => 'Zebra', 'deadline' => now()]);
        $route = "/admin/companies/{$company->id}/tasks";

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
        $company = Company::factory()->for($exhibition)->create();
        $task = Task::factory()
            ->for($company)
            ->create(['title' => 'Zebra', 'deadline' => now()]);
        $route = "/admin/companies/{$company->id}/tasks";

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
        $company = Company::factory()->for($exhibition)->create();
        $task = Task::factory()
            ->for($company)
            ->create(['title' => 'Zebra', 'deadline' => now()]);
        $route = "/admin/companies/{$company->id}/tasks";

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
        $company = Company::factory()->for($exhibition)->create();
        $task = Task::factory()
            ->for($company)
            ->create(['title' => 'Zebra', 'deadline' => now()]);
        $route = "/admin/companies/{$company->id}/tasks";

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
        $date1 = now();
        Date::setLocale('ru');

        $tasks = Task::factory()
            ->for($company)
            ->createMany([
                ['title' => 'Alpha', 'deadline' => $date1],
                ['title' => 'Beta', 'deadline' => $date2],
                ['title' => 'Zebra', 'deadline' => $date3],
            ]);
        $route = "/admin/companies/{$company->id}/tasks";

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
        $page->assertSeeIn('#tasks-table tr:first-child td:nth-child(2)', $date3->translatedFormat('j M. Y'));
        $page->assertSeeIn('#tasks-table tr:nth-child(3) td:nth-child(2)', $date1->translatedFormat('j M. Y'));
        $page->click('Дедлайн');
        $page->assertSeeIn('#tasks-table tr:first-child td:nth-child(2)', $date1->translatedFormat('j M. Y'));
        $page->assertSeeIn('#tasks-table tr:nth-child(3) td:nth-child(2)', $date3->translatedFormat('j M. Y'));

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
        $route = "/admin/companies/{$company->id}/tasks";

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
        $page->assertSee('Выйти');
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
        $page->assertSee('Выйти');
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
});
