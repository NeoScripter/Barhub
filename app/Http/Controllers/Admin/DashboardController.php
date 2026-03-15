<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Exhibition;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

final class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index(Request $request)
    {
        $query = Exhibition::query()->select(['id', 'name', 'starts_at', 'is_active']);
        $tasks = null;
        $activeExhibition = $request->user()?->getActiveExhibition();

        if ($request->user()->role !== UserRole::SUPER_ADMIN) {
            $query->whereHas('users', function ($q) use ($request): void {
                $q->where('user_id', $request->user()->id);
            });
        }

        if ($activeExhibition) {
            $tasks = Task::forSummary($activeExhibition->id);
        }

        $expos = $query->get();

        return Inertia::render('admin/Dashboard/Dashboard', [
            'expos' => $expos,
            'tasks' => $tasks,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $request->user()->setActiveExhibition($id);
        $request->user()->save();

        return back();
    }
}
