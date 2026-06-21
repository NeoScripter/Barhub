<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

final class IntergrationController extends Controller
{
    public function index()
    {
        $path = storage_path() . '/logs/integration.log';
        exec("tail -n 15 {$path}", $output);

        return Inertia::render('admin/Integration/Index', [
            'output' => $output,
        ]);
    }
}
