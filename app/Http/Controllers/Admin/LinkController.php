<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Person;
use App\Models\Event;
use Inertia\Inertia;

class LinkController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        $people = Person::select(['id', 'name'])
            ->whereHas('events')
            ->get()
            ->map(fn($person) => ['id' => $person->id, 'value' => $person->name])
            ->values();

        $events = Event::select(['id', 'title'])
            ->get()
            ->map(fn($event) => ['id' => $event->id, 'value' => $event->title])
            ->values();

        $companies = Company::select(['id', 'public_name'])
            ->get()
            ->map(fn($company) => ['id' => $company->id, 'value' => $company->public_name])
            ->values();

        return Inertia::render('admin/Links/Index', [
            'people' => $people,
            'events' => $events,
            'companies' => $companies,
        ]);
    }
}
