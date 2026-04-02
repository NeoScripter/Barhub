<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Actions\AttachRolesToPeople;
use App\Enums\PersonRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\EventIndexRequest;
use App\Models\Event;
use App\Models\Exhibition;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class EventController extends Controller
{
    public function index(EventIndexRequest $request, AttachRolesToPeople $action, Exhibition $exhibition)
    {
        unset($request);

        $eventsQuery = QueryBuilder::for($exhibition->events())
            ->with(['stage', 'themes', 'people'])
            ->allowedFilters([
                AllowedFilter::exact('stage.name'),
                AllowedFilter::exact('themes.name'),
                'starts_at',
            ]);

        /** @var Event[] $events */
        $events = $action->execute($eventsQuery->get());

        $allEvents = $exhibition->events()->with(['stage', 'themes'])->get();

        /** @var string[] $themes */
        $themes = $exhibition
            ->themes()
            ->pluck('name')
            ->values();

        /** @var string[] $stages */
        $stages = $exhibition
            ->stages()
            ->pluck('name')
            ->values();

        /** @var string[] $days */
        $days = $allEvents
            ->pluck('starts_at')
            ->sort()
            ->unique()
            ->map(fn($date) => $date->format('Y-m-d'))
            ->values();

        return Inertia::render('user/Events/Index', [
            'events' => $events,
            'themes' => $themes,
            'stages' => $stages,
            'days' => $days,
            'exhibition' => $exhibition,
        ]);
    }

    public function show(Exhibition $exhibition, Event $event)
    {
        $event->load(['stage', 'themes',
            'people' => fn($query) => $query->withPivot('role')]);

        $event->people->transform(function ($person) {
            $person->role_label =
                PersonRole::from($person->pivot->role)->label();
            return $person;
        });

        return Inertia::render('user/Events/Show', [
            'exhibition' => $exhibition,
            'event' => $event,
        ]);
    }
}
