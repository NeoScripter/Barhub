<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Company\CompanyDestroyRequest;
use App\Http\Requests\Admin\Company\CompanyIndexRequest;
use App\Http\Requests\Admin\Company\CompanyStoreRequest;
use App\Http\Requests\Admin\Company\CompanyUpdateRequest;
use App\Models\Company;
use App\Models\Image;
use App\Models\Tag;
use App\Sorts\ManyToManySort;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

final class CompanyController extends Controller
{
    public function index(CompanyIndexRequest $request)
    {
        $exhibition = Auth::user()->getActiveExhibition();
        /** @var LengthAwarePaginator<Company> $companies */
        $companies = QueryBuilder::for(
            Company::query()->select(['id', 'public_name', 'legal_name', 'stand_code', 'show_on_site'])
                ->where('exhibition_id', $exhibition->id)
                ->with(['tags', 'tasks:id,status,company_id'])
                ->withCount('followups')
        )
            ->allowedSorts([
                'public_name',
                AllowedSort::custom('tags.name', new ManyToManySort(
                    pivotTable: 'company_tag',
                    relatedTable: 'tags',
                    pivotForeignKey: 'company_id',
                    pivotRelatedKey: 'tag_id',
                    column: 'name',
                )),
            ])

            ->withSearch('public_name', $request->string('search'))
            ->paginate()
            ->appends($request->query());

        return Inertia::render('admin/Companies/Index', [
            'companies' => $companies,
            'tags' => Tag::query()->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        $tags = Tag::query()->orderBy('name')->get();

        return Inertia::render('admin/Companies/Create', [
            'tags' => $tags,
        ]);
    }

    public function store(CompanyStoreRequest $request)
    {
        DB::transaction(function () use ($request): void {
            $exhibition = Auth::user()->getActiveExhibition();
            $validated = $request->validated();

            $company = Company::query()->create([
                ...Arr::except($validated, ['tags', 'logo']),
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
                    $company->public_name,
                );
            }
        });

        return to_route('admin.companies.index')
            ->with('success', 'Компания успешно создана');
    }

    public function edit(Company $company)
    {
        $tags = Tag::query()->orderBy('name')->get();

        $company->load(['tags', 'exhibition']);

        return Inertia::render('admin/Companies/Edit', [
            'company' => $company,
            'tags' => $tags,
        ]);
    }

    public function update(CompanyUpdateRequest $request, Company $company)
    {
        DB::transaction(function () use ($request, $company): void {
            $validated = $request->validated();
            $company->update(Arr::except($validated, ['tags', 'logo']));

            if ($request->has('tags')) {
                $company->tags()->sync($request->input('tags', []));
            }

            if ($request->hasFile('logo')) {
                if ($company->logo) {
                    $company->logo->updateImage(
                        $request->file('logo'),
                        $company->public_name,
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
                        $company->public_name,
                    );
                }
            }
        });

        return to_route('admin.companies.index')
            ->with('success', 'Компания успешно обновлена');
    }

    public function destroy(CompanyDestroyRequest $request, Company $company)
    {
        $company->logo?->delete();
        $company->delete();

        return to_route('admin.companies.index')
            ->with('success', 'Компания успешно удалена');
    }
}
