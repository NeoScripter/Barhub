<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Partner\PartnerUpdateRequest;
use App\Http\Requests\Admin\Task\TaskIndexRequest;
use App\Models\Exhibition;
use App\Models\Task;
use App\Sorts\RelationSort;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

final class PartnerController extends Controller
{
    public function index(TaskIndexRequest $request)
    {
        $exhibition = Auth::user()->getActiveExhibition();

        $tasks = QueryBuilder::for(Task::query()->select(['tasks.title', 'tasks.id', 'tasks.deadline', 'tasks.status', 'tasks.company_id'])
            ->forExhibition($exhibition->id))
            ->with('company:public_name,id')
            ->where('status', '!=', TaskStatus::COMPLETED)
            ->allowedSorts([
                'title',
                'deadline',
                'status',
                AllowedSort::custom('company.public_name', new RelationSort('companies', 'public_name', 'company_id')),
            ])
            ->paginate()
            ->through(fn($task): array => [
                ...$task->toArray(),
                'status' => $task->status->label(),
            ])
            ->appends($request->query());

        $summary = Task::forSummary($exhibition->id);

        return Inertia::render('admin/Partners/Index', [
            'tasks' => $tasks,
            'summary' => $summary,
        ]);
    }

    public function edit(Task $allTask)
    {
        $task = $allTask;
        $task->load([
            'company:public_name,id',
            'comments' => fn($q) => $q->with(['file', 'user'])->orderBy('created_at'),
        ]);

        return Inertia::render('admin/Partners/Edit', [
            'task' => $task,
        ]);
    }

    public function update(PartnerUpdateRequest $request, Task $allTask)
    {
        abort_if($allTask->status !== TaskStatus::TO_BE_VERIFIED, 403);
        $task = $allTask;
        $newStatus = $request->boolean('is_accepted') === true ? TaskStatus::COMPLETED : TaskStatus::INCOMPLETE;
        $task->update(['status' => $newStatus]);

        return to_route('admin.all-tasks.index');
    }
}
