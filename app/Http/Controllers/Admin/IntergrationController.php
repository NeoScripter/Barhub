<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Integration;
use Illuminate\Http\Request;
use Inertia\Inertia;

final class IntergrationController extends Controller
{
    public function index()
    {
        $path = storage_path() . '/logs/integration.log';
        exec("tail -n 15 {$path}", $output);

        $integration = Integration::firstOrCreate();

        return Inertia::render('admin/Integration/Index', [
            'output' => $output,
            'status' => $integration->status,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate(['on' => 'boolean']);
        $integration = Integration::firstOrCreate();

        $integration->update(['status' => $validated['on']]);

        return redirect()->back();
    }
}
