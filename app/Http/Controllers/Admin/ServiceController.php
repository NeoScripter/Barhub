<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Service\ServiceIndexRequest;
use App\Http\Requests\Admin\Service\ServiceStoreRequest;
use App\Http\Requests\Admin\Service\ServiceUpdateRequest;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;

final class ServiceController extends Controller
{
    public function index(ServiceIndexRequest $request)
    {
        $exhibition = Auth::user()->getActiveExhibition();

        if (!$exhibition) {
            return redirect()->route('admin.dashboard');
        }

        $services = QueryBuilder::for($exhibition->services()->select(['name', 'description', 'id']))
            ->allowedSorts(['name'])
            ->paginate()
            ->appends($request->query());

        return Inertia::render('admin/Services/Index', [
            'services' => $services,
        ]);
    }


    public function edit(Service $service)
    {
        Gate::authorize('view', $service->exhibition);

        return Inertia::render('admin/Services/Edit', [
            'service' => $service,
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/Services/Create');
    }

    public function store(ServiceStoreRequest $request)
    {
        $exhibition = Auth::user()->getActiveExhibition();

        if (!$exhibition) {
            return redirect()->route('admin.dashboard');
        }

        $exhibition->services()->create(
            $request->only(['name', 'id', 'description', 'is_active'])
        );

        return to_route('admin.services.index');
    }

    public function update(ServiceUpdateRequest $request, Service $service)
    {
        Gate::authorize('view', $service->exhibition);

        $service->update($request->only(['name', 'id', 'description', 'is_active']));

        return to_route('admin.services.index');
    }

    public function destroy(Service $service)
    {
        Gate::authorize('view', $service->exhibition);

        $service->delete();

        return to_route('admin.services.index');
    }
}
