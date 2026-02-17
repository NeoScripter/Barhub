<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Exhibition;
use Illuminate\Http\Request;
use Inertia\Inertia;

final class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $query = Exhibition::query()->select(['id', 'name', 'starts_at', 'is_active']);

        if ($request->user()->role !== UserRole::SUPER_ADMIN) {
            $query->whereHas('users', function ($q) use ($request): void {
                $q->where('user_id', $request->user()->id);
            });
        }

        $expos = $query->get();

        return Inertia::render('admin/Dashboard/Dashboard', [
            'expos' => $expos,
        ]);
    }
}
