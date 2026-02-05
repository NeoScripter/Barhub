<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exhibition;
use Inertia\Inertia;

final class ExhibitionController extends Controller
{
    public function index()
    {
        /** @var array<int, Exhibition> $expos */
        $expos = Exhibition::all();

        return Inertia::render('admin/Exhibitions', [
            'exhibitions' => $expos,
        ]);
    }
}
