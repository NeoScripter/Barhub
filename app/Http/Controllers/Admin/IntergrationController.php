<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Jobs\Integration\FullSyncJob;
use App\Models\Integration;
use Illuminate\Http\Request;
use Inertia\Inertia;

final class IntergrationController extends Controller
{
    public function index()
    {
        $path = storage_path('logs/integration.log');
        $output = [];

        if (is_file($path)) {
            exec('tail -n 15 ' . escapeshellarg($path), $output);
        }

        $integration = Integration::firstOrCreate();

        return Inertia::render('admin/Integration/Index', [
            'output' => $output,
            'status' => $integration->status,
        ]);
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== UserRole::SUPER_ADMIN) {
            abort(403, 'You must be a super admin to run synchronization');
        }

        // Одна джоба синхронизирует всё в правильном порядке:
        // темы → залы → экспоненты → спикеры → расписание
        FullSyncJob::dispatch();

        return redirect()->back()
            ->with('success', 'Синхронизация запущена, результат появится в логе ниже');
    }

    public function update(Request $request)
    {
        $validated = $request->validate(['on' => 'boolean']);
        $integration = Integration::firstOrCreate();

        $integration->update(['status' => $validated['on']]);

        return redirect()->back();
    }
}
