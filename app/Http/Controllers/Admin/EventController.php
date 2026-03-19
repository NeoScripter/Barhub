<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\FormatEventPeople;
use App\Enums\PersonRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Event\EventDestroyRequest;
use App\Http\Requests\Admin\Event\EventIndexRequest;
use App\Http\Requests\Admin\Event\EventStoreRequest;
use App\Http\Requests\Admin\Event\EventUpdateRequest;
use App\Models\Event;
use App\Models\Stage;
use App\Models\Theme;
use App\Sorts\RelationSort;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

final class EventController extends Controller
{
    public function index(EventIndexRequest $request)
    {
        $exhibition = Auth::user()->getActiveExhibition();
        /** @var LengthAwarePaginator<Event> $events */
        $events = QueryBuilder::for($exhibition->events())
            ->with([
                'stage',
                'themes',
                'people:name,id',
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
            'events' => $events,
            'stages' => Stage::query()->select(['id', 'name'])->get(),
            'themes' => Theme::all(),
        ]);
    }
    public function create()
    {
        $exhibition = Auth::user()->getActiveExhibition();

        return Inertia::render('admin/Events/Create', [
            'stages' => Stage::query()->select(['id', 'name'])->get(),
            'themes' => Theme::all(),
            'availablePeople' => $exhibition->people()->select(['id', 'name'])->get(),
            'roles' => PersonRole::toSelectList(),
            'exhibition' => $exhibition,
        ]);
    }

    public function store(EventStoreRequest $request)
    {
        DB::transaction(function () use ($request) {
            $event = $request->user()
                ->getActiveExhibition()
                ->events()
                ->create($request->only([
                    'title',
                    'description',
                    'stage_id',
                    'starts_at',
                    'ends_at',
                ]));

            if ($request->has('theme_ids')) {
                $event->themes()->attach($request->theme_ids);
            }

            if ($request->has('people')) {
                foreach ($request->people as $personData) {
                    foreach ($personData['roles'] as $role) {
                        $event->people()->attach($personData['person_id'], ['role' => $role]);
                    }
                }
            }

            return $event;
        });

        return to_route('admin.events.index')
            ->with('success', 'Событие успешно создано');
    }

    public function edit(
        Event $event,
        FormatEventPeople $formatPeople
    ) {
        Gate::authorize('view', $event->exhibition);

        $event->load(['stage', 'themes', 'people', 'exhibition']);

        return Inertia::render('admin/Events/Edit', [
            'event' => $event,
            'eventPeople' => $formatPeople->execute($event),
            'availablePeople' => $event->exhibition->people()->select(['id', 'name'])->get(),
            'roles' => PersonRole::toSelectList(),
            'stages' => Stage::query()->select(['id', 'name'])->get(),
            'themes' => Theme::all(),
        ]);
    }

    public function update(EventUpdateRequest $request, Event $event)
    {
        Gate::authorize('view', $event->exhibition);
        DB::transaction(function () use ($request, $event): void {
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

        return to_route('admin.events.index')
            ->with('success', 'Event updated successfully');
    }


    public function destroy(EventDestroyRequest $request, Event $event)
    {
        $event->delete();

        return to_route('admin.events.index')
            ->with('success', 'Событие успешно удалено');
    }
}
