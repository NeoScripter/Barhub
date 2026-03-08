<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ServiceStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Service\ServiceStoreRequest;
use App\Http\Requests\Admin\Service\ServiceUpdateRequest;
use App\Models\Company;
use App\Models\Exhibition;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index(Request $request, Exhibition $exhibition, Company $company)
    {
        $services = $company->services()->select(['name', 'id', 'placeholder', 'description'])
            ->paginate()
            ->appends($request->query());

        return Inertia::render('admin/Services/Index', [
            'exhibition' => $exhibition,
            'company' => $company,
            'services' => $services,
        ]);
    }

    public function edit(Exhibition $exhibition, Company $company, Service $service)
    {
        return Inertia::render('admin/Services/Edit', [
            'exhibition' => $exhibition,
            'company'    => $company,
            'service'       => $service,
        ]);
    }

    public function create(Exhibition $exhibition, Company $company)
    {
        return Inertia::render('admin/Services/Create', [
            'exhibition' => $exhibition,
            'company'    => $company,
        ]);
    }

    public function store(ServiceStoreRequest $request, Exhibition $exhibition, Company $company)
    {
        $company->services()->create(
            $request->only(['name', 'id', 'placeholder', 'description'])
        );

        return redirect()->route('admin.exhibitions.services.index', [
            'exhibition' => $exhibition,
            'company' => $company,
        ]);
    }

    public function update(ServiceUpdateRequest $request, Exhibition $exhibition, Company $company, Service $service)
    {
        $service->update($request->only(['name', 'id', 'placeholder', 'description']));

        return redirect()->route('admin.exhibitions.services.index', [
            'exhibition' => $exhibition,
            'company' => $company,
        ]);
    }

    public function destroy(Exhibition $exhibition, Company $company, Service $service)
    {
        $service->delete();

        return redirect()->route('admin.exhibitions.services.index', [
            'exhibition' => $exhibition,
            'company' => $company,
        ]);
    }
}
