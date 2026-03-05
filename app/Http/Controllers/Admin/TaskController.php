<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Task\TaskIndexRequest;
use App\Http\Requests\Admin\Task\TaskStoreRequest;
use App\Http\Requests\Admin\Task\TaskUpdateRequest;
use App\Models\Company;
use App\Models\Exhibition;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;

class TaskController extends Controller
{
    public function index(TaskIndexRequest $request, Exhibition $exhibition, Company $company)
    {
        $tasks = QueryBuilder::for($company->tasks()->select(['title', 'id', 'deadline', 'status']))
            ->allowedSorts(['title', 'deadline', 'status'])
            ->paginate()
            ->through(fn($task) => [
                ...$task->toArray(),
                'status' => $task->status->label(),
            ])
            ->appends($request->query());

        return Inertia::render('admin/Tasks/Index', [
            'exhibition' => $exhibition,
            'company' => $company,
            'tasks' => $tasks,
        ]);
    }

    public function edit(Exhibition $exhibition, Company $company, Task $task)
    {
        $task->load(['comments', 'files']);

        return Inertia::render('admin/Tasks/Edit', [
            'exhibition' => $exhibition,
            'company'    => $company,
            'task'       => $task,
        ]);
    }

    public function create(Exhibition $exhibition, Company $company)
    {
        return Inertia::render('admin/Tasks/Create', [
            'exhibition' => $exhibition,
            'company'    => $company,
        ]);
    }

    public function store(TaskStoreRequest $request, Exhibition $exhibition, Company $company)
    {
        $task = $company->tasks()->create([
            ...$request->only(['title', 'description', 'deadline']),
            'status' => TaskStatus::TO_BE_COMPLETED
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('task-files');
            $task->files()->create([
                'name' => $request->validated('file_name'),
                'url'  => $path,
            ]);
        }

        if ($request->filled('comment')) {
            $task->comments()->create([
                'content' => $request->validated('comment'),
            ]);
        }

        return redirect()->back();
    }

    public function update(TaskUpdateRequest $request, Exhibition $exhibition, Company $company, Task $task)
    {
        $task->update($request->only(['title', 'description', 'deadline']));

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('task-files');
            $task->files()->create([
                'name' => $request->validated('file_name'),
                'url'  => $path,
            ]);
        }

        // if ($request->filled('comment')) {
        //     $task->comments()->oldest()->update([
        //         'content' => $request->validated('comment'),
        //     ]);
        // }

        return redirect()->back();
    }

    public function destroy(Exhibition $exhibition, Company $company, Task $task)
    {
        foreach ($task->files as $file) {
            Storage::delete($file->url);
        }

        $task->delete();

        return redirect()->back();
    }
}
