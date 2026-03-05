<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Task\TaskIndexRequest;
use App\Models\Company;
use App\Models\Exhibition;
use App\Models\Task;
use App\Models\User;
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
        return Inertia::render('admin/Companies/Edit', [
            'exhibition' => $exhibition,
            'company'    => $company,
            'task'       => $task,
        ]);
    }

    public function create(Exhibition $exhibition, Company $company)
    {
        return Inertia::render('admin/Companies/Create', [
            'exhibition' => $exhibition,
            'company'    => $company,
        ]);
    }


    // public function store(Exhibition $exhibition, Company $company)
    // {
    //     $user = User::find($id);
    //     $company->users()->save($user);
    //     $user->update(['role' => UserRole::task]);
    //     $user->save();

    //     return redirect()->back();
    // }

    // public function update(Exhibition $exhibition, Company $company, int $id)
    // {
    //     $user = User::find($id);
    //     $company->users()->save($user);
    //     $user->update(['role' => UserRole::task]);
    //     $user->save();

    //     return redirect()->back();
    // }

    // public function destroy(Exhibition $exhibition, Company $company, int $id)
    // {
    //     $user = User::find($id);
    //     $user->update(['company_id' => null, 'role' => UserRole::USER]);
    //     $user->save();

    //     return redirect()->back();
    // }
}
