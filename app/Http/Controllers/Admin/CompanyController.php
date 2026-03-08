<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Company\CompanyIndexRequest;
use App\Http\Requests\Admin\Company\CompanyStoreRequest;
use App\Http\Requests\Admin\Company\CompanyUpdateRequest;
use App\Models\Company;
use App\Models\Exhibition;
use App\Models\Image;
use App\Models\Tag;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;

final class CompanyController extends Controller
{
    public function index(CompanyIndexRequest $request, Exhibition $exhibition)
    {
        /** @var LengthAwarePaginator<Company> $companies */
        $companies = QueryBuilder::for(
            Company::query()->select(['id', 'public_name', 'legal_name', 'stand_code'])
                ->where('exhibition_id', $exhibition->id)
                ->with(['tags', 'tasks:id,status,company_id'])
        )
            ->allowedSorts(['public_name'])
            ->withSearch('public_name', $request->string('search'))
            ->paginate()
            ->appends($request->query());

        return Inertia::render('admin/Companies/Index', [
            'exhibition' => $exhibition,
            'companies' => $companies,
        ]);
    }

    public function create(Exhibition $exhibition)
    {
        $tags = Tag::query()->orderBy('name')->get();

        return Inertia::render('admin/Companies/Create', [
            'exhibition' => $exhibition,
            'tags' => $tags,
        ]);
    }

    public function store(CompanyStoreRequest $request, Exhibition $exhibition)
    {
        DB::transaction(function () use ($request, $exhibition): void {
            $validated = $request->validated();

            $company = Company::query()->create([
                ...Arr::except($validated, ['tags', 'logo', 'logo_alt']),
                'exhibition_id' => $exhibition->id,
            ]);

            if ($request->filled('tags')) {
                $company->tags()->sync($request->input('tags'));
            }

            if ($request->hasFile('logo')) {
                Image::attachToModel(
                    $company,
                    $request->file('logo'),
                    'logo',
                    'companies/logos',
                    400,
                    $request->input('logo_alt', '')
                );
            }
        });

        return to_route('admin.exhibitions.companies.index', $exhibition)
            ->with('success', 'Компания успешно создана');
    }

    public function edit(Exhibition $exhibition, Company $company)
    {
        $tags = Tag::query()->orderBy('name')->get();

        $company->load(['tags']);

        return Inertia::render('admin/Companies/Edit', [
            'exhibition' => $exhibition,
            'company' => $company,
            'tags' => $tags,
        ]);
    }

    public function update(CompanyUpdateRequest $request, Exhibition $exhibition, Company $company)
    {
        DB::transaction(function () use ($request, $company): void {
            $validated = $request->validated();
            $company->update(Arr::except($validated, ['tags', 'logo', 'logo_alt']));

            if ($request->has('tags')) {
                $company->tags()->sync($request->input('tags', []));
            }

            if ($request->hasFile('logo')) {
                if ($company->logo) {
                    $company->logo->updateImage(
                        $request->file('logo'),
                        $request->input('logo_alt'),
                        'companies/logos',
                        400
                    );
                } else {
                    Image::attachToModel(
                        $company,
                        $request->file('logo'),
                        'logo',
                        'companies/logos',
                        400,
                        $request->input('logo_alt', '')
                    );
                }
            } elseif ($request->has('logo_alt') && $company->logo) {
                $company->logo->updateImage(null, $request->input('logo_alt'));
            }
        });

        return to_route('admin.exhibitions.companies.index', $exhibition)
            ->with('success', 'Компания успешно обновлена');
    }

    public function destroy(Exhibition $exhibition, Company $company)
    {
        $company->logo?->delete();
        $company->delete();

        return to_route('admin.exhibitions.companies.index', $exhibition)
            ->with('success', 'Компания успешно удалена');
    }
}
