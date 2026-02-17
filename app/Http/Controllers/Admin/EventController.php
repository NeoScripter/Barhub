<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exhibition;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index(Exhibition $exhibition)
    {
        $events = $exhibition->events;

        return Inertia::render('admin/Events/Index', [
            'exhibition' => $exhibition,
            'events' => $events,
        ]);
    }
}
