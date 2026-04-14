<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TaskTemplate\TaskTemplateIndexRequest;
use App\Http\Requests\Admin\TaskTemplate\TaskTemplateStoreRequest;
use App\Http\Requests\Admin\TaskTemplate\TaskTemplateUpdateRequest;
use App\Models\TaskTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;

final class TaskTemplateController extends Controller
{
    public function index(TaskTemplateIndexRequest $request)
    {
        $exhibition = Auth::user()->getActiveExhibition();

        $templates = QueryBuilder::for($exhibition->taskTemplates()->select(['title', 'id', 'deadline']))
            ->allowedSorts(['title', 'deadline'])
            ->paginate()
            ->appends($request->query());

        return Inertia::render('admin/TaskTemplates/Index', [
            'templates' => $templates,
        ]);
    }

    public function edit(TaskTemplate $taskTemplate)
    {
        Gate::authorize('view', $taskTemplate->exhibition);

        return Inertia::render('admin/TaskTemplates/Edit', [
            'template' => $taskTemplate,
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/TaskTemplates/Create');
    }

    public function store(TaskTemplateStoreRequest $request)
    {
        $exhibition = Auth::user()->getActiveExhibition();

        $user = Auth::user();
        $template = $exhibition->taskTemplates()->create([
            ...$request->only(['title', 'description', 'deadline', 'comment', 'file_name']),
            'user_id' => $user->id,
            'status' => $request->boolean('to_be_checked') ? TaskStatus::TO_BE_COMPLETED : TaskStatus::COMPLETED,
        ]);


        if ($request->hasFile('file_url')) {
            $path = $request->file('file_url')->store('task-template-files', 'public');
            $template->update([
                'file_url' => $path,
            ]);
        }

        return to_route('admin.task-templates.index');
    }

    public function update(TaskTemplateUpdateRequest $request, TaskTemplate $taskTemplate)
    {
        Gate::authorize('view', $taskTemplate->exhibition);
        $taskTemplate->update($request->only(['title', 'description', 'deadline', 'comment', 'file_name']));

        if ($request->hasFile('file_url')) {
            if ($taskTemplate->file_url) {
                Storage::delete($taskTemplate->file_url);
            }
            $path = $request->file('file_url')->store('task-template-files', 'public');
            $taskTemplate->update(['file_url' => $path]);
        }

        return to_route('admin.task-templates.index');
    }

    public function destroy(TaskTemplate $taskTemplate)
    {
        $taskTemplate->delete();

        return to_route('admin.task-templates.index');
    }
}
