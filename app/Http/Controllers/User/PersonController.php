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

        return Inertia::render('user/People/Show', [
            'exhibition' => $exhibition,
            'person' => $person,
        ]);
    }
}
