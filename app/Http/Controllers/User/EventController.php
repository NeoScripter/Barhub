<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Exhibition;
use App\Models\Stage;
use Inertia\Inertia;

final class EventController extends Controller
{
    public function index(Exhibition $exhibition)
    {
        // /** @var \App\Models\Event[] $events */
        $events = $exhibition
            ->events()
            ->with(['stage', 'themes', 'organizer'])
            ->get();

        /** @var string[] $themes */
        $themes = $events
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
        $days = $events
            ->pluck('starts_at')
            ->map(fn($date) => $date->format('d.m'))
            ->unique()
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
