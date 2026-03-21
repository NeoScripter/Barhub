<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\FollowupStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CompanyFollowup\CompanyFollowupStoreRequest;
use App\Http\Requests\Admin\CompanyFollowup\CompanyFollowupUpdateRequest;
use App\Models\Company;
use App\Models\Followup;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;

final class CompanyFollowupController extends Controller
{
    public function index(Company $company)
    {
        Gate::authorize('view', $company->exhibition);
        $followups = QueryBuilder::for($company->followups()->select(['id', 'name', 'description', 'comment', 'status']))
            ->where('status', FollowupStatus::COMPLETED)
            ->get();

        return Inertia::render('admin/CompanyFollowups/Index', [
            'followups' => $followups,
            'company' => $company,
        ]);
    }

    public function create(Company $company)
    {
        Gate::authorize('view', $company->exhibition);

        return Inertia::render('admin/CompanyFollowups/Create', [
            'company' => $company,
        ]);
    }

    public function store(CompanyFollowupStoreRequest $request,  Company $company)
    {
        Gate::authorize('view', $company->exhibition);

        $company->followups()->create([
            ...$request->only(['id', 'name', 'comment', 'description']),
            'status' => FollowupStatus::COMPLETED
        ]);

        return to_route('admin.companies.followups.index', [
            'company' => $company,
        ]);
    }

    public function edit(Company $company, Followup $followup)
    {
        Gate::authorize('view', $company->exhibition);

        return Inertia::render('admin/CompanyFollowups/Edit', [
            'followup' => $followup,
            'company' => $company,
        ]);
    }

    public function update(CompanyFollowupUpdateRequest $request, Company $company, Followup $followup)
    {
        Gate::authorize('view', $company->exhibition);
        $followup->update($request->only(['id', 'name', 'comment', 'description']));

        return to_route('admin.companies.followups.index', [
            'company' => $company,
        ]);
    }

    public function destroy(Company $company, Followup $followup)
    {
        Gate::authorize('view', $company->exhibition);

        $followup->delete();

        return to_route('admin.companies.followups.index', [
            'company' => $company,
        ]);
    }
}
