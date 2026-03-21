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
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

describe('Exponent Task Test', function (): void {
    beforeEach(function (): void {
        $this->exhibition = Exhibition::factory()->create();
        $this->company = Company::factory()->for($this->exhibition)->create();
        $this->exponent = User::factory()->for($this->company)->create();
        $this->exponent->assignRole(UserRole::EXPONENT);
    });

    it('renders the task edit page with all the comments', function (): void {
        $task = Task::factory()->for($this->company)->create(['status' => TaskStatus::TO_BE_COMPLETED]);

        TaskComment::factory()->count(3)->for($task)->create();

        actingAs($this->exponent)
            ->get(route('exponent.tasks.edit', [$task]))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page
                    ->component('exponent/Tasks/Edit')
                    ->has('task.comments', 3)
            );
    });

    it('doesnt allow to update a task without comment', function (): void {
        $task = Task::factory()->for($this->company)->create(['status' => TaskStatus::TO_BE_COMPLETED]);

        actingAs($this->exponent)
            ->put(route('exponent.tasks.update', $task), [
                'comment' => '',
            ])
            ->assertSessionHasErrors('comment');
    });

    it('updates the task status to to be verified after updating it', function (): void {
        $task = Task::factory()->for($this->company)->create(['status' => TaskStatus::TO_BE_COMPLETED]);

        actingAs($this->exponent)
            ->put(route('exponent.tasks.update', $task), [
                'comment' => 'This is my completed work description.',
            ])
            ->assertRedirect(route('exponent.tasks.index'));

        \Pest\Laravel\assertDatabaseHas('tasks', [
            'id'     => $task->id,
            'status' => TaskStatus::TO_BE_VERIFIED->value,
        ]);
    });

    it('attaches a task file when updating it', function (): void {
        Storage::fake('local');

        $task = Task::factory()->for($this->company)->create(['status' => TaskStatus::TO_BE_COMPLETED]);
        $file = UploadedFile::fake()->create('document.pdf', 100);

        actingAs($this->exponent)
            ->put(route('exponent.tasks.update', $task), [
                'comment'   => 'This is my completed work description.',
                'file'      => $file,
                'file_name' => 'document.pdf',
            ])
            ->assertRedirect(route('exponent.tasks.index'));

        $comment = $task->comments()->latest()->first();
        expect($comment->file)->not->toBeNull();
        Storage::assertExists($comment->file->url);
    });

    it('sorts the tasks from most urgent to least urgent on the index page', function (): void {
        Task::factory()->for($this->company)->create([
            'status'   => TaskStatus::TO_BE_COMPLETED,
            'deadline' => now()->addDays(10),
        ]);
        Task::factory()->for($this->company)->create([
            'status'   => TaskStatus::TO_BE_COMPLETED,
            'deadline' => now()->addDays(1),
        ]);
        Task::factory()->for($this->company)->create([
            'status'   => TaskStatus::TO_BE_COMPLETED,
            'deadline' => now()->addDays(5),
        ]);

        $tasks = actingAs($this->exponent)
            ->get(route('exponent.tasks.index'))
            ->assertOk()
            ->viewData('page')['props']['tasks'];

        expect($tasks[0]['deadline'])->toBeLessThan($tasks[1]['deadline'])
            ->and($tasks[1]['deadline'])->toBeLessThan($tasks[2]['deadline']);
    });

    it('displays only the tasks assigned to the exponent company on the page', function (): void {
        $otherCompany = Company::factory()->for($this->exhibition)->create();

        Task::factory(4)->for($this->company)->create(['status' => TaskStatus::TO_BE_COMPLETED]);
        Task::factory(3)->for($otherCompany)->create(['status' => TaskStatus::TO_BE_COMPLETED]);

        $tasks = actingAs($this->exponent)
            ->get(route('exponent.tasks.index'))
            ->assertOk()
            ->viewData('page')['props']['tasks'];

        expect(count($tasks))->toBe(4 + 1);
    });

    it('does not display the tasks with completed and to be verified status', function (): void {
        Task::factory()->for($this->company)->create(['status' => TaskStatus::TO_BE_COMPLETED]);
        Task::factory()->for($this->company)->create(['status' => TaskStatus::DELAYED]);
        Task::factory()->for($this->company)->create(['status' => TaskStatus::INCOMPLETE]);
        Task::factory()->for($this->company)->create(['status' => TaskStatus::COMPLETED]);
        Task::factory()->for($this->company)->create(['status' => TaskStatus::TO_BE_VERIFIED]);

        $tasks = actingAs($this->exponent)
            ->get(route('exponent.tasks.index'))
            ->assertOk()
            ->viewData('page')['props']['tasks'];

        expect(count($tasks))->toBe(3 + 1);
    });

    it('redirects guest users to login', function (): void {
        get(route('exponent.tasks.index'))
            ->assertRedirect(route('login'));
    });

    it('redirects authenticated exponent to exponent dashboard after login', function (): void {
        $this->post(route('login'), [
            'email'    => $this->exponent->email,
            'password' => 'password',
        ])
            ->assertRedirect(route('exponent.tasks.index'));
    });

    it('allows EXPONENT role to access exponent tasks index', function (): void {
        actingAs($this->exponent)
            ->get(route('exponent.tasks.index'))
            ->assertOk()
            ->assertInertia(
                fn($page) => $page->component('exponent/Tasks/Index')
            );
    });

    it('forbids USER role from accessing exponent tasks', function (): void {
        $user = User::factory()->create();
        $user->assignRole(UserRole::USER);

        actingAs($user)
            ->get(route('exponent.tasks.index'))
            ->assertForbidden();
    });

    it('forbids ADMIN role from accessing exponent tasks', function (): void {
        $admin = User::factory()->create();
        $admin->assignRole(UserRole::ADMIN);

        actingAs($admin)
            ->get(route('exponent.tasks.index'))
            ->assertForbidden();
    });

    it('forbids SUPER_ADMIN role from accessing exponent tasks', function (): void {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(UserRole::SUPER_ADMIN);

        actingAs($superAdmin)
            ->get(route('exponent.tasks.index'))
            ->assertForbidden();
    });

    it('redirects unauthenticated users to login page', function (): void {
        get(route('exponent.tasks.index'))
            ->assertRedirect(route('login'));
    });
});
