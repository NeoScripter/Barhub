<?php

declare(strict_types=1);

namespace App\Http\Controllers\Exponent;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

final class CompanyController extends Controller
{
    public function index()
    {
        $company = Auth::user()->company;
        abort_unless($company, 404, 'Компания не найдена');
        $company->load(['tags']);

        return Inertia::render('exponent/Companies/Index', [
            'company' => $company,
        ]);
    }

    public function edit(Company $company)
    {
        return Inertia::render('exponent/Companies/Edit', [
            'company' => $company,
        ]);
    }


    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'public_name' => ['sometimes', 'string', 'min:1', 'max:255'],
            'description' => ['sometimes', 'string', 'min:10', 'max:5000'],
            'phone' => ['sometimes', 'string', 'max:50'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:companies,email,' . $company->id],
            'site_url' => ['nullable', 'url', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'telegram' => ['nullable', 'string', 'max:255'],
            'activities' => ['nullable', 'string', 'max:5000'],
            'logo' => ['nullable', 'image', 'max:51200'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
        ]);

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

        return to_route('exponent.companies.index')
            ->with('success', 'Компания успешно обновлена');
    }
}
