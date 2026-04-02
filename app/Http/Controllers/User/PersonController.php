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
