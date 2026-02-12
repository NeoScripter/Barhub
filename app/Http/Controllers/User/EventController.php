<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Exhibition;
use Inertia\Inertia;

final class EventController extends Controller
{
    public function index(Exhibition $exhibition)
    {
        /** @var \App\Models\Event[] $events */
        $events = $exhibition
            ->events()
            ->with(['stage', 'themes', 'organizer'])
            ->get();

        return Inertia::render('user/Events/Events', [
            'exhibition' => $exhibition,
            'events' => $events,
        ]);
    }

    public function show(Event $event)
    {
        return Inertia::render('user/Events/Events', [
            'event' => $event,
        ]);
    }
}
