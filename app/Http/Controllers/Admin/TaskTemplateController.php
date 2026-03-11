<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TaskTemplate\TaskTemplateIndexRequest;
use App\Http\Requests\Admin\TaskTemplate\TaskTemplateStoreRequest;
use App\Http\Requests\Admin\TaskTemplate\TaskTemplateUpdateRequest;
use App\Models\Exhibition;
use App\Models\TaskTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;

final class TaskTemplateController extends Controller
{
    public function index(TaskTemplateIndexRequest $request, Exhibition $exhibition)
    {
        $templates = QueryBuilder::for($exhibition->tasks()->select(['title', 'id', 'deadline']))
            ->allowedSorts(['title', 'deadline'])
            ->paginate()
            ->appends($request->query());

        return Inertia::render('admin/TaskTemplates/Index', [
            'exhibition' => $exhibition,
            'tasks' => $templates,
        ]);
    }

    public function edit(Exhibition $exhibition, TaskTemplate $taskTemplate)
    {
        return Inertia::render('admin/TaskTemplates/Edit', [
            'exhibition' => $exhibition,
            'template' => $taskTemplate,
        ]);
    }

    public function create(Exhibition $exhibition)
    {
        return Inertia::render('admin/TaskTemplates/Create', [
            'exhibition' => $exhibition,
        ]);
    }

    public function store(TaskTemplateStoreRequest $request, Exhibition $exhibition)
    {
        $user = Auth::user();
        $template = $exhibition->taskTemplates()->create([
            ...$request->only(['title', 'description', 'deadline', 'comment', 'file_name']),
            'user_id' => $user->id,
        ]);

        if ($request->hasFile('file_url')) {
            $path = $request->file('file_url')->store('task-template-files');
            $template->update([
                'url' => $path,
            ]);
        }

        return to_route('admin.exhibitions.task-templates.index', [
            'exhibition' => $exhibition,
        ]);
    }

    public function update(TaskTemplateUpdateRequest $request, Exhibition $exhibition, TaskTemplate $taskTemplate)
    {
        $taskTemplate->update($request->only(['title', 'description', 'deadline', 'comment', 'file_name']));

        if ($request->hasFile('file_url')) {
            $path = $request->file('file_url')->store('task-template-files');
            Storage::delete($path);
            $taskTemplate->update(['file_url' => $path]);
        }

        return to_route('admin.exhibitions.task-templates.index', [
            'exhibition' => $exhibition,
        ]);
    }

    public function destroy(Exhibition $exhibition, TaskTemplate $taskTemplate)
    {
        $taskTemplate->delete();

        return to_route('admin.exhibitions.task-templates.index', [
            'exhibition' => $exhibition,
        ]);
    }
}
