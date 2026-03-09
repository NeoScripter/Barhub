<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Exhibition;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

final class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $query = Exhibition::query()->select(['id', 'name', 'starts_at', 'is_active']);
        $tasks = null;

        if ($request->user()->role !== UserRole::SUPER_ADMIN) {
            $query->whereHas('users', function ($q) use ($request): void {
                $q->where('user_id', $request->user()->id);
            });
        }

        if ($request->has('selected')) {
            $expo = Exhibition::findOrFail($request->integer('selected'));

            if (Gate::check('viewAny', $expo)) {
                $tasks = Task::select(['status', DB::raw('count(*) as count')])
                    ->where('status', '!=', TaskStatus::COMPLETED)
                    ->join('companies', 'exhibition_id', '=', 'tasks.company_id')
                    ->where('companies.exhibition_id', $expo->id)
                    ->groupBy('status')
                    ->get()
                    ->map(fn($task): array => [
                        'count' => $task->count,
                        'status' => $task->status->label(),
                    ]);
            }
        }

        $expos = $query->get();

        return Inertia::render('admin/Dashboard/Dashboard', [
            'expos' => $expos,
            'tasks' => $tasks,
        ]);
    }
}
