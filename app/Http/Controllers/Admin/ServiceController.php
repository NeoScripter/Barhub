<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Service\ServiceStoreRequest;
use App\Http\Requests\Admin\Service\ServiceUpdateRequest;
use App\Models\Company;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

final class ServiceController extends Controller
{
    public function index(Request $request, Company $company)
    {
        Gate::authorize('view', $company->exhibition);

        $services = $company->services()->select(['name', 'id', 'placeholder', 'description'])
            ->paginate()
            ->appends($request->query());

        return Inertia::render('admin/Services/Index', [
            'company' => $company,
            'services' => $services,
        ]);
    }

    public function edit(Company $company, Service $service)
    {
        Gate::authorize('view', $company->exhibition);

        return Inertia::render('admin/Services/Edit', [
            'company' => $company,
            'service' => $service,
        ]);
    }

    public function create(Company $company)
    {
        Gate::authorize('view', $company->exhibition);

        return Inertia::render('admin/Services/Create', [
            'company' => $company,
        ]);
    }

    public function store(ServiceStoreRequest $request,  Company $company)
    {
        Gate::authorize('view', $company->exhibition);

        $company->services()->create(
            $request->only(['name', 'id', 'placeholder', 'description', 'is_active'])
        );

        return to_route('admin.services.index', [
            'company' => $company,
        ]);
    }

    public function update(ServiceUpdateRequest $request,  Company $company, Service $service)
    {
        Gate::authorize('view', $company->exhibition);

        $service->update($request->only(['name', 'id', 'placeholder', 'description', 'is_active']));

        return to_route('admin.services.index', [
            'company' => $company,
        ]);
    }

    public function destroy(Company $company, Service $service)
    {
        Gate::authorize('view', $company->exhibition);

        $service->delete();

        return to_route('admin.services.index', [
            'company' => $company,
        ]);
    }
}
