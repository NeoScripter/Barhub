<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Exhibition\TaskIndexRequest;
use App\Models\Company;
use App\Models\Exhibition;
use App\Models\User;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;

class TaskController extends Controller
{
    public function index(TaskIndexRequest $request, Exhibition $exhibition, Company $company)
    {

        $tasks = QueryBuilder::for($company->tasks(['name', 'id', 'deadline', 'status']))
            ->allowedSorts(['name', 'deadline', 'status'])
            ->paginate()
            ->appends($request->query());

        return Inertia::render('admin/Tasks/Index', [
            'exhibition' => $exhibition,
            'company' => $company,
            'tasks' => $tasks,
            'users' => $users,
        ]);
    }

    public function update(Exhibition $exhibition, Company $company, int $id)
    {
        $user = User::find($id);
        $company->users()->save($user);
        $user->update(['role' => UserRole::task]);
        $user->save();

        return redirect()->back();
    }

    public function destroy(Exhibition $exhibition, Company $company, int $id)
    {
        $user = User::find($id);
        $user->update(['company_id' => null, 'role' => UserRole::USER]);
        $user->save();

        return redirect()->back();
    }
}
