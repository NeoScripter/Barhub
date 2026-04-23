<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Person;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LinkController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        $exhibition = Auth::user()->getActiveExhibition();
        if (!$exhibition) {
            return redirect()->route('admin.dashboard');
        }
        $people = $exhibition
            ->people()
            ->select(['id', 'name'])
            ->whereHas('events')
            ->get()
            ->map(fn($person) => ['id' => $person->id, 'value' => $person->name])
            ->values();

        $events = $exhibition
            ->events()
            ->select(['id', 'title'])
            ->get()
            ->map(fn($event) => ['id' => $event->id, 'value' => $event->title])
            ->values();

        $companies = $exhibition
            ->companies()
            ->select(['id', 'public_name'])
            ->get()
            ->map(fn($company) => ['id' => $company->id, 'value' => $company->public_name])
            ->values();

        return Inertia::render('admin/Links/Index', [
            'people' => $people,
            'events' => $events,
            'companies' => $companies,
            'exhibition' => $exhibition,
        ]);
    }
}
