<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

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
        $tasks = QueryBuilder::for(Task::select(['title', 'id', 'deadline', 'status', 'company_id']))
            ->with('company:public_name,id')
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

        $summary = Task::forExhibition($exhibition->id);

        return Inertia::render('admin/Partners/Index', [
            'exhibition' => $exhibition,
            'tasks' => $tasks,
            'summary' => $summary,
        ]);
    }

    public function edit(Exhibition $exhibition, Task $allTask)
    {
        $task = $allTask;
        $task->load(['company:public_name,id', 'comments.file' => fn($q) => $q->latest()]);

        return Inertia::render('admin/Partners/Edit', [
            'exhibition' => $exhibition,
            'task' => $task,
        ]);
    }

    public function update(PartnerUpdateRequest $request, Exhibition $exhibition) {}
}
