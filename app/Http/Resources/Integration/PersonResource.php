<?php

declare(strict_types=1);

namespace App\Http\Resources\Integration;

use App\Models\Person;
use Illuminate\Support\Facades\DB;

class PersonResource
{
    public static function make(Person $person): array
    {
        $speaker_ids = DB::select(
            'SELECT p.id FROM people p
            JOIN person_person
            ON person_id = ?
            AND person_id = p.id',
            [$person->id]
        );

        $speaker_ids = array_map(
            fn($arr) => $arr->id,
            $speaker_ids
        );

        $full_name = trim($person->name);
        $full_name = preg_replace('/\s+/', ' ', $full_name);
        [$first_name, $last_name] = explode(' ', $full_name);

        return [
            'id'                => $person->id,
            'firstName'          => $first_name,
            'lastName'          => $last_name,
            'description'          => $person->relalia,
            'isSpeaker'          => true,
            'position'          => 'Неизвестно',
            'company'             =>  'Неизвестно',
            'externalImagePath' => $person?->avatar->webp ?? url('placeholder.webp'),
        ];
    }
}
