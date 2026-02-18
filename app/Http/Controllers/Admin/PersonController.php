<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PersonIndexRequest;
use App\Models\Exhibition;
use App\Models\Person;
use App\Sorts\RelationSort;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

final class PersonController extends Controller
{
    public function index(PersonIndexRequest $request, Exhibition $exhibition)
    {
        /** @var LengthAwarePaginator<Person> $persons */
        $persons = QueryBuilder::for($exhibition->persons())
            ->with([
                'stage',
                'themes',
                'people' => fn ($query) => $query
                    ->select('people.id', 'people.name')
                    ->groupBy('people.id', 'people.name'),
            ])
            ->allowedSorts([
                'title',
                'starts_at',
                AllowedSort::custom('stage.name', new RelationSort('stages', 'name', 'stage_id')),
            ])
            ->when($request->string('search'), function ($query, $search): void {
                $query->where(function ($q) use ($search): void {
                    $q->whereRaw('title LIKE ?', ["%{$search}%"]);
                });
            })
            ->paginate()
            ->appends($request->query());

        return Inertia::render('admin/People/Index', [
            'exhibition' => $exhibition,
            'persons' => $persons,
        ]);
    }

    public function edit(Exhibition $exhibition, Person $person)
    {

        return Inertia::render('admin/People/Edit', [
            'exhibition' => $exhibition,
            'person' => $person,
        ]);
    }
}
