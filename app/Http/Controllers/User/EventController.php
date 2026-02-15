<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Exhibition;
use App\Models\Stage;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class EventController extends Controller
{
    public function index(Exhibition $exhibition)
    {
        $eventsQuery = QueryBuilder::for($exhibition->events())
            ->with(['stage', 'themes', 'organizer'])
            ->allowedFilters([
                AllowedFilter::exact('stage.name'),
                AllowedFilter::exact('themes.name'),
                'starts_at'
            ]);

        /** @var \App\Models\Event[] $events */
        $events = $eventsQuery->get();

        $allEvents = $exhibition->events()->with(['stage', 'themes'])->get();

        /** @var string[] $themes */
        $themes = $allEvents
            ->pluck('themes')
            ->flatten()
            ->pluck('name')
            ->unique()
            ->values();

        /** @var string[] $stages */
        $stages = Stage::query()
            ->distinct()
            ->pluck('name');

        /** @var string[] $days */
        $days = $allEvents
            ->pluck('starts_at')
            ->sort()
            ->unique()
            ->map(fn($date) => $date->format('Y-m-d'))
            ->values();

        return Inertia::render('user/Events/Events', [
            'exhibition' => $exhibition,
            'events' => $events,
            'themes' => $themes,
            'stages' => $stages,
            'days' => $days,
        ]);
    }

    public function show(Event $event)
    {
        return Inertia::render('user/Events/Events', [
            'event' => $event,
        ]);
    }
}
