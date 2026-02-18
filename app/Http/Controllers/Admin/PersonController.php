<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Person\PersonIndexRequest;
use App\Models\Exhibition;
use App\Models\Person;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;

final class PersonController extends Controller
{
    public function index(PersonIndexRequest $request, Exhibition $exhibition)
    {
        $eventIds = $exhibition->events()->pluck('id');

        $people = QueryBuilder::for(Person::whereHas('events', fn($q) => $q->whereIn('events.id', $eventIds)))
            ->select('people.*')
            ->withCount('events')
            ->allowedSorts(['name'])
            ->withSearch('name', $request->string('search'))
            ->paginate()
            ->through(fn($person) => tap($person, function ($p) use ($eventIds) {
                $p->roles = $p->roles($eventIds->toArray());
            }));

        return Inertia::render('admin/People/Index', [
            'exhibition' => $exhibition,
            'people' => $people,
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
