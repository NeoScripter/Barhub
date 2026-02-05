<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\UserPermission;
use App\Http\Controllers\Controller;
use App\Models\Exhibition;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

final class ExhibitionController extends Controller
{
    public function index()
    {
        /** @var \Illuminate\Pagination\LengthAwarePaginator<Exhibition> $expos */
        $expos = Exhibition::paginate();

        return Inertia::render('admin/Exhibitions/Exhibitions', [
            'expos' => $expos,
            'isSuperAdmin' => Auth::user()->hasPermissionTo(UserPermission::MANAGE_EXHIBITIONS)
        ]);
    }
}
