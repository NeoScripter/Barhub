<?php

declare(strict_types=1);

namespace App\Http\Controllers\Exponent;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Exponent\Task\TaskUpdateRequest;
use App\Models\Company;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

final class TaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        abort_unless($user->company, 403, 'Попросите администратора назначить компанию на ваш аккаунт');
        $tasks = $user->company
            ->tasks()
            ->whereIn('status', [TaskStatus::TO_BE_COMPLETED, TaskStatus::DELAYED, TaskStatus::INCOMPLETE])
            ->orderBy('deadline')
            ->get()
            ->map(fn($task): array => [
                ...$task->toArray(),
                'status' => $task->status->label(),
                'date' => Carbon::parse($task->deadline)->format('d'),
                'month' => Carbon::parse($task->deadline)->locale('ru')->translatedFormat('M'),
            ]);

        return Inertia::render('exponent/Tasks/Index', [
            'tasks' => $tasks,
            'company' => $user->company->public_name,
        ]);
    }

    public function edit(Company $company, Task $task)
    {
        $task->load([
            'comments' => fn($q) => $q
                ->with(['file', 'user'])
                ->orderBy('created_at'),
        ]);

        return Inertia::render('exponent/Tasks/Edit', [
            'company' => $company,
            'task' => $task,
        ]);
    }

    public function update(TaskUpdateRequest $request, Task $task)
    {
        $user = Auth::user();

        $comment = $task->comments()->create([
            'content' => $request->validated('comment'),
            'user_id' => $user->id,
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('task-files');

            $comment->file()->create([
                'name' => $request->validated('file_name'),
                'url' => $path,
            ]);
        }

        $task->update(['status' => TaskStatus::TO_BE_VERIFIED]);

        return to_route('exponent.tasks.index');
    }
}
