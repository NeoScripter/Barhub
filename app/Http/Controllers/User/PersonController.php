<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Enums\PersonRole;
use App\Http\Controllers\Controller;
use App\Models\Exhibition;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

final class PersonController extends Controller
{
    public function index(Request $request, Exhibition $exhibition)
    {
        $roles = array_filter(
            explode(',', $request->input('filter.roles', ''))
        );

        $query = empty($roles) ? '' :
            "and pivot2.role in (" . implode(',', array_fill(0, count($roles), '?')) . ")";

        $people = DB::select(
            '
            select p.id, p.name, p.regalia, p.bio, p.telegram, img.*,
            group_concat(distinct pivot2.role) as roles from people as p
            join exhibition_person as pivot1
            on pivot1.person_id = p.id
            join event_person as pivot2
            on pivot2.person_id = p.id
            join images as img
            on img.imageable_id = p.id
            and img.imageable_type = "person"
            and img.type = "avatar"
            where pivot1.exhibition_id = ?'
                . $query . ' group by p.id',
            array_merge([$exhibition->id],  $roles)
        );

        $roles = DB::select('select distinct role from event_person');

        $roles = array_map(
            fn($role) => [
                'label' => PersonRole::from((int) $role->role)->label(),
                'key' => (string) $role->role
            ],
            $roles
        );

        foreach ($people as $person) {
            $person->role_label = implode(
                ', ',
                array_map(
                    fn($role) => PersonRole::from((int) $role)->label(),
                    explode(',', $person->roles)
                )
            );
        }

        $people = array_map(
            fn($person) => [
                'id' => $person->id,
                'role_label' => $person->role_label,
                'avatar' => [
                    'webp3x' => $person->webp3x,
                    'webp2x' => $person->webp2x,
                    'webp' => $person->webp,
                    'avif3x' => $person->avif3x,
                    'avif2x' => $person->avif2x,
                    'avif' => $person->avif,
                    'tiny' => $person->tiny,
                    'alt' => $person->alt,
                ],
               'name' => $person->name,
                'regalia' => $person->regalia,
                'telegram' => $person->telegram,
                'bio' => $person->bio,
            ],
            $people
        );

        return Inertia::render('user/People/Index', [
            'exhibition' => $exhibition,
            'people' => $people,
            'roles' => $roles,
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
