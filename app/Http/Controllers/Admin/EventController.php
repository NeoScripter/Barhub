<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\FormatEventPeople;
use App\Enums\PersonRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Event\EventIndexRequest;
use App\Http\Requests\Admin\Event\EventUpdateRequest;
use App\Models\Event;
use App\Models\Exhibition;
use App\Models\Person;
use App\Models\Stage;
use App\Models\Theme;
use App\Sorts\RelationSort;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
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
                'people:name,id'
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

    public function edit(
        Exhibition $exhibition,
        Event $event,
        FormatEventPeople $formatPeople
    ) {
        $event->load(['stage', 'themes', 'people']);

        return Inertia::render('admin/Events/Edit', [
            'exhibition' => $exhibition,
            'event' => $event,
            'eventPeople' => $formatPeople->execute($event),
            'stages' => Stage::select(['id', 'name'])->get(),
            'themes' => Theme::all(),
            'availablePeople' => Person::select(['id', 'name'])->get(),
            'roles' => PersonRole::toSelectList(),
        ]);
    }

    public function update(EventUpdateRequest $request, Exhibition $exhibition, Event $event)
    {
        DB::transaction(function () use ($request, $event) {
            $event->update($request->only([
                'title',
                'description',
                'stage_id',
                'starts_at',
                'ends_at',
            ]));

            if ($request->has('theme_ids')) {
                $event->themes()->sync($request->theme_ids);
            }

            if ($request->has('people')) {
                $event->people()->detach();

                foreach ($request->people as $personData) {
                    foreach ($personData['roles'] as $role) {
                        $event->people()->attach($personData['person_id'], ['role' => $role]);
                    }
                }
            }
        });

        return redirect()
            ->route('admin.exhibitions.events.index', $exhibition)
            ->with('success', 'Event updated successfully');
    }
}
