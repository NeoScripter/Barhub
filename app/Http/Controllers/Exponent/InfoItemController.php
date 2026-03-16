<?php

declare(strict_types=1);

namespace App\Http\Controllers\Exponent;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

final class InfoItemController extends Controller
{
    public function index()
    {
        $company = Auth::user()->company;
        abort_unless($company != null, 404, 'Компания не найдена');
        $infoItems = $company->exhibition->infoItems;

        return Inertia::render('exponent/InfoItems/Index', [
            'infoItems' => $infoItems,
        ]);
    }
}
