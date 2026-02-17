<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exhibition;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use App\Models\Event;
use App\Sorts\RelationSort;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class EventController extends Controller
{
    public function index(Request $request, Exhibition $exhibition)
    {
        /** @var LengthAwarePaginator<Event> $events */
        $events = QueryBuilder::for($exhibition->events())
            ->with([
                'stage',
                'themes',
                'people' => fn($query) => $query
                    ->select('people.id', 'people.name')
                    ->groupBy('people.id', 'people.name')
            ])
            ->allowedSorts([
                'title',
                'starts_at',
                AllowedSort::custom('stage.name', new RelationSort('stages', 'name', 'stage_id')),
            ])
            ->paginate()
            ->appends($request->query());

        return Inertia::render('admin/Events/Index', [
            'exhibition' => $exhibition,
            'events' => $events,
        ]);
    }

    public function edit(Exhibition $exhibition, Event $event)
    {

        return Inertia::render('admin/Events/Edit', [
            'exhibition' => $exhibition,
            'event' => $event,
        ]);
    }
}
