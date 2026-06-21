<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

final class IntergrationController extends Controller
{
    public function index()
    {

        return Inertia::render('admin/Integration/Index', [
            'logs' => 'logs',
        ]);
    }
}
