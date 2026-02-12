<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exhibition;
use Inertia\Inertia;

final class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        $expo = Exhibition::query()->select(['id', 'name', 'starts_at'])->get();

        return Inertia::render('admin/Dashboard/Dashboard', [
            'expos' => $expo,
        ]);
    }
}
