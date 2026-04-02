<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Task\TaskIndexRequest;
use App\Http\Requests\Admin\Task\TaskStoreRequest;
use App\Http\Requests\Admin\Task\TaskUpdateRequest;
use App\Models\Company;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;

final class TaskController extends Controller
{
    public function index(TaskIndexRequest $request, Company $company)
    {
        Gate::authorize('view', $company->exhibition);

        $tasks = QueryBuilder::for($company->tasks()->select(['title', 'id', 'deadline', 'status']))
            ->allowedSorts(['title', 'deadline', 'status'])
            ->paginate()
            ->through(fn($task): array => [
                ...$task->toArray(),
                'status' => $task->status->label(),
            ])
            ->appends($request->query());

        return Inertia::render('admin/Tasks/Index', [
            'company' => $company,
            'tasks' => $tasks,
        ]);
    }

    public function edit(Company $company, Task $task)
    {
        Gate::authorize('view', $company->exhibition);

        $user = Auth::user();
        $task->load(['comments' => function ($q) use ($user): void {
            $q->where('user_id', $user->id)
                ->latest()
                ->limit(1)
                ->with('file');
        }]);

        return Inertia::render('admin/Tasks/Edit', [
            'company' => $company,
            'task' => $task,
        ]);
    }

    public function create(Company $company)
    {
        Gate::authorize('view', $company->exhibition);

        return Inertia::render('admin/Tasks/Create', [
            'company' => $company,
        ]);
    }

    public function store(TaskStoreRequest $request, Company $company)
    {
        Gate::authorize('view', $company->exhibition);

        $task = $company->tasks()->create([
            ...$request->only(['title', 'description', 'deadline']),
            'status' => TaskStatus::TO_BE_COMPLETED,
        ]);

        if ($request->filled('comment') || $request->hasFile('file')) {
            $comment = $task->comments()->create([
                'content' => $request->validated('comment') ?? '',
                'user_id' => $request->user()->id
            ]);

            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('task-files', 'public');
                $comment->file()->create([
                    'name' => $request->validated('file_name'),
                    'url' => $path,
                ]);
            }
        }

        return to_route('admin.tasks.index', [
            'company' => $company,
        ]);
    }

    public function update(TaskUpdateRequest $request, Company $company, Task $task)
    {
        Gate::authorize('view', $company->exhibition);

        $task->update($request->only(['title', 'description', 'deadline']));

        if ($request->filled('comment') || $request->hasFile('file')) {
            $user = Auth::user();
            $comment = $task->comments()->where('user_id', $user->id)
                ->latest()
                ->first();

            if ($comment) {
                $comment->update([
                    'content' => $request->validated('comment') ?? '',
                ]);
            } else {
                $comment = $task->comments()->create([
                    'content' => $request->validated('comment') ?? '',
                    'user_id' => $user->id,
                ]);
            }

            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('task-files', 'public');

                $comment->file?->delete();
                $comment->file()->create([
                    'name' => $request->validated('file_name'),
                    'url' => $path,
                ]);
            }
        }

        return to_route('admin.tasks.index', [
            'company' => $company,
        ]);
    }

    public function destroy(Company $company, Task $task)
    {
        Gate::authorize('view', $company->exhibition);

        $task->delete();

        return to_route('admin.tasks.index', [
            'company' => $company,
        ]);
    }
}
