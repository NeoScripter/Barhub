<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Enums\PersonRole;
use App\Http\Controllers\Controller;
use App\Models\Exhibition;
use App\Models\Person;
use Inertia\Inertia;

final class PersonController extends Controller
{
    public function index(Exhibition $exhibition)
    {
        $people = $exhibition->people()
            ->with(['events' => function ($query) use ($exhibition) {
                $query->withPivot('role')
                    ->whereHas('exhibition', fn($q) => $q->where('exhibitions.id', $exhibition->id));
            }])
            ->get()
            ->transform(function ($person) {
                $firstRole = $person->events->first()?->pivot?->role;
                $person->role_label = $firstRole
                    ? PersonRole::from($firstRole)->label()
                    : null;
                return $person;
            });

        return Inertia::render('user/People/Index', [
            'exhibition' => $exhibition,
            'people' => $people,
        ]);
    }

    public function show(Exhibition $exhibition, Person $person)
    {
        $person->load('events');

        $person->events->transform(function ($event) {
            $event->role_label =
                PersonRole::from($event->pivot->role)->label();
            return $event;
        });

        return Inertia::render('user/People/Show', [
            'exhibition' => $exhibition,
            'person' => $person,
        ]);
    }
}
