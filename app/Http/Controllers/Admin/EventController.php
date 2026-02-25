<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

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
                'people' => fn($query) => $query
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
        // Load event with people and their pivot roles
        $event->load(['stage', 'themes', 'people']);

        // Transform people to include their role for this specific event
        $eventPeople = $event->people->map(function ($person) {
            return [
                'person_id' => $person->id,
                'role' => $person->pivot->role,
            ];
        });

        return Inertia::render('admin/Events/Edit', [
            'exhibition' => $exhibition,
            'event' => $event,
            'eventPeople' => $eventPeople,
            'stages' => Stage::select(['id', 'name'])->get(),
            'themes' => Theme::all(),
            'availablePeople' => Person::select(['id', 'name'])->get(),
            'roles' => collect(PersonRole::cases())->map(fn($role) => [
                'value' => $role->value,
                'label' => $role->label(),
            ]),
        ]);
    }

    public function update(EventUpdateRequest $request, Exhibition $exhibition, Event $event)
    {
        DB::transaction(function () use ($request, $event) {
            // Update basic fields
            $event->update($request->only([
                'title',
                'description',
                'stage_id',
                'starts_at',
                'ends_at',
            ]));

            // Sync themes
            if ($request->has('theme_ids')) {
                $event->themes()->sync($request->theme_ids);
            }

            // Sync people with roles
            if ($request->has('people')) {
                // Build sync data: [person_id => ['role' => role_value]]
                $peopleData = collect($request->people)->mapWithKeys(function ($item) {
                    return [$item['person_id'] => ['role' => $item['role']]];
                });

                $event->people()->sync($peopleData);
            }
        });

        return redirect()
            ->route('admin.exhibitions.events.index', $exhibition)
            ->with('success', 'Event updated successfully');
    }
}
