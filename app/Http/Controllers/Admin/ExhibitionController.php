<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exhibition;
use Inertia\Inertia;

class ExhibitionController extends Controller
{
    public function index()
    {
        /** @var array<int, Exhibition> $exhibitions */
        $expos = Exhibition::all();

        return Inertia::render('admin/Dashboard', [
            'exhibitions' => $expos
        ]);
    }
}
