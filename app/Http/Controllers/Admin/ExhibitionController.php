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
        // Query building
        $query = Exhibition::query();

        // If not super admin, only show exhibitions assigned to this user
        if ($request->user()->role !== UserRole::SUPER_ADMIN) {
            $query->whereHas('users', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            });
        }

        /** @var LengthAwarePaginator<Exhibition> $exhibitions */
        $exhibitions = QueryBuilder::for($query)
            ->allowedSorts(['name', 'starts_at', 'ends_at', 'location', 'is_active'])
            ->paginate()
            ->appends($request->query());

        return Inertia::render('admin/Exhibitions/Index', [
            'expos' => $exhibitions,
            'isSuperAdmin' => $request->user()->role === UserRole::SUPER_ADMIN
        ]);
    }

    public function edit(Exhibition $exhibition): Response
    {
        return Inertia::render('admin/Exhibitions/Edit', [
            'exhibition' => $exhibition
        ]);
    }
}
