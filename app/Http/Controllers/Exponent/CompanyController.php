<?php

declare(strict_types=1);

namespace App\Http\Controllers\Exponent;

use App\Http\Controllers\Controller;
use App\Http\Requests\exponent\Company\CompanyUpdateRequest;
use App\Models\Company;
use App\Models\Image;
use App\Models\Tag;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

final class CompanyController extends Controller
{
    public function show(Company $company)
    {
        $company->load(['tags']);

        return Inertia::render('exponent/Companies/Edit', [
            'company' => $company,
        ]);
    }
    public function edit(Company $company)
    {
        $company->load(['tags']);

        return Inertia::render('exponent/Companies/Edit', [
            'company' => $company,
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

        return to_route('exponent.companies.index')
            ->with('success', 'Компания успешно обновлена');
    }
}
