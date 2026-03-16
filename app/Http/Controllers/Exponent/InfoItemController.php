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
        $exhibition = Auth::user()->getActiveExhibition();
        $infoItems = $exhibition->infoItems()
            ->paginate();

        return Inertia::render('exponent/InfoItems/Index', [
            'infoItems' => $infoItems,
        ]);
    }
}
