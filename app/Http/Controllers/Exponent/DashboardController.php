<?php

declare(strict_types=1);

namespace App\Http\Controllers\Exponent;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

final class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        $user = Auth::user();
        $tasks = Task::forExponent($user->company->id);

        return Inertia::render('exponent/Dashboard/Dashboard', [
            'tasks' => $tasks
        ]);
    }
}
