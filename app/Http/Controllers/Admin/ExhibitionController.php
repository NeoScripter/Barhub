<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Exhibition\ExhibitionUpdateRequest;
use App\Models\Exhibition;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\QueryBuilder;

final class ExhibitionController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Exhibition::query();

        if ($request->user()->role !== UserRole::SUPER_ADMIN) {
            $query->whereHas('users', function ($q) use ($request): void {
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
            'isSuperAdmin' => $request->user()->role === UserRole::SUPER_ADMIN,
        ]);
    }

    public function edit(Exhibition $exhibition): Response
    {
        return Inertia::render('admin/Exhibitions/Edit', [
            'exhibition' => $exhibition,
        ]);
    }

    public function show(Exhibition $exhibition): Response
    {
        return Inertia::render('admin/Exhibitions/Show', [
            'exhibition' => $exhibition,
        ]);
    }

    public function update(ExhibitionUpdateRequest $request, Exhibition $exhibition)
    {
        $exhibition->update($request->validated());

        return redirect()->back();
    }

}
