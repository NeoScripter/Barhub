<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Exhibition;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\QueryBuilder;

final class ExhibitionController extends Controller
{
    public function index(Request $request): Response
    {
        /** @var LengthAwarePaginator<Exhibition> $expos */
        $expos = QueryBuilder::for(Exhibition::class)
            ->allowedSorts(['name', 'starts_at', 'ends_at', 'location', 'is_active'])
            ->paginate()
            ->appends(request()->query());

        return Inertia::render('admin/Exhibitions/Exhibitions', [
            'expos' => $expos,
            'isSuperAdmin' => $request->user()->role === UserRole::SUPER_ADMIN
        ]);
    }
}
