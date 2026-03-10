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
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

final class PartnerController extends Controller
{
    public function index(TaskIndexRequest $request, Exhibition $exhibition)
    {
        $tasks = QueryBuilder::for(Task::select(['tasks.title', 'tasks.id', 'tasks.deadline', 'tasks.status', 'tasks.company_id'])
            ->forExhibition($exhibition->id))
            ->with('company:public_name,id')
            ->where('status', TaskStatus::TO_BE_VERIFIED)
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
            'exhibition' => $exhibition,
            'tasks' => $tasks,
            'summary' => $summary,
        ]);
    }

    public function edit(Exhibition $exhibition, Task $allTask)
    {
        if ($allTask->status !== TaskStatus::TO_BE_VERIFIED) {
            abort(403);
        }

        $task = $allTask;
        $task->load([
            'company:public_name,id',
            'comments' => fn($q) => $q->with(['file', 'user'])->orderBy('created_at')
        ]);

        return Inertia::render('admin/Partners/Edit', [
            'exhibition' => $exhibition,
            'task' => $task,
        ]);
    }

    public function update(PartnerUpdateRequest $request, Exhibition $exhibition, Task $allTask)
    {
        if ($allTask->status !== TaskStatus::TO_BE_VERIFIED) {
            abort(403);
        }
        $task = $allTask;
        $newStatus = $request->boolean('is_accepted') === true ? TaskStatus::COMPLETED : TaskStatus::IMCOMPLETE;
        $task->update(['status' => $newStatus]);

        return to_route('admin.exhibitions.all-tasks.index', [
            'exhibition' => $exhibition
        ]);
    }
}
