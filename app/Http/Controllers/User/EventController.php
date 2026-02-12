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
        return Inertia::render('user/Events/Events', [
            'exhibition' => $exhibition,
        ]);
    }

    public function show(Event $event)
    {
        return Inertia::render('user/Events/Events', [
            'event' => $event,
        ]);
    }
}
