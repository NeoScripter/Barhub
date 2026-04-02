<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Exhibition;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function index(Request $request, Exhibition $exhibition)
    {
        return Inertia::render('user/Companies/Index', [
            'companies' => $exhibition->companies()
                ->with('tags')
                ->withSearch(
                    'public_name',
                    $request->string('search')
                )
                ->get()
        ]);
    }

    public function show(Exhibition $exhibition, Company $company)
    {
        return Inertia::render('user/Companies/Show', [
            'company' => $company
        ]);
    }
}
