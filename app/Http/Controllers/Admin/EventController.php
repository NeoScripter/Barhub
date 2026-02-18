<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Event\EventIndexRequest;
use App\Models\Event;
use App\Models\Exhibition;
use App\Sorts\RelationSort;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

final class EventController extends Controller
{
    public function index(EventIndexRequest $request, Exhibition $exhibition)
    {
        /** @var LengthAwarePaginator<Event> $events */
        $events = QueryBuilder::for($exhibition->events())
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
