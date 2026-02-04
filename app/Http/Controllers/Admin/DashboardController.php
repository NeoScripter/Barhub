<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exhibition;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        /** @var array<int, Exhibition> $exhibitions */
        $expos = Exhibition::all();

        return Inertia::render('admin/Dashboard', [
            'exhibitions' => $expos
        ]);
    }
}
