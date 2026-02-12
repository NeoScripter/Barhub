<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Exhibition;
use Inertia\Inertia;

final class ExhibitionController extends Controller
{
    public function index()
    {
        $expos = Exhibition::select(['name', 'id'])->get();

        return Inertia::render('user/Exhibitions', [
            'expos' => $expos,
        ]);
    }
}
