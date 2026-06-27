<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Jobs\Integration\SyncCompanyJob;
use App\Jobs\Integration\SyncEventJob;
use App\Jobs\Integration\SyncPersonJob;
use App\Jobs\Integration\SyncStageJob;
use App\Jobs\Integration\SyncThemeJob;
use App\Models\Company;
use App\Models\Event;
use App\Models\Integration;
use App\Models\Person;
use App\Models\Stage;
use App\Models\Theme;
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

    public function store(Request $request)
    {
        if ($request->user()->role !== UserRole::SUPER_ADMIN) {
            abort(403, 'You must be a super admin to run synchronization');
        }

        Theme::all()->each(fn($theme) => SyncThemeJob::dispatch($theme, 'create'));
        Stage::all()->each(fn($stage) => SyncStageJob::dispatch($stage, 'create'));
        Company::all()->each(fn($company) => SyncCompanyJob::dispatch($company, 'create'));
        Person::all()->each(fn($person) => SyncPersonJob::dispatch($person, 'create'));
        Event::all()->each(fn($event) => SyncEventJob::dispatch($event, 'create'));

        return redirect()->back();
    }

    public function update(Request $request)
    {
        $validated = $request->validate(['on' => 'boolean']);
        $integration = Integration::firstOrCreate();

        $integration->update(['status' => $validated['on']]);

        return redirect()->back();
    }
}
