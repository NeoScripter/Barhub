<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Partner\PartnerUpdateRequest;
use App\Http\Requests\Admin\Task\TaskIndexRequest;
use App\Models\Exhibition;
use App\Models\Task;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;

final class PartnerController extends Controller
{
    public function index(TaskIndexRequest $request, Exhibition $exhibition)
    {
        $tasks = QueryBuilder::for(Task::select(['title', 'id', 'deadline', 'status', 'company_id'])->with('company:public_name,id'))
            ->allowedSorts(['title', 'deadline', 'status'])
            ->paginate()
            ->through(fn ($task): array => [
                ...$task->toArray(),
                'status' => $task->status->label(),
            ])
            ->appends($request->query());

        return Inertia::render('admin/Partners/Index', [
            'exhibition' => $exhibition,
            'tasks' => $tasks,
        ]);
    }

    public function edit(Exhibition $exhibition, Task $task)
    {
        $task->load(['company.public_name']);

        return Inertia::render('admin/Partners/Edit', [
            'exhibition' => $exhibition,
            'task' => $task,
        ]);
    }

    public function update(PartnerUpdateRequest $request, Exhibition $exhibition)
    {
    }
}
