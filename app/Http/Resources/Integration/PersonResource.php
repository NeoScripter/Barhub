<?php

declare(strict_types=1);

namespace App\Http\Resources\Integration;

use App\Models\Person;
use Illuminate\Support\Facades\DB;

class PersonResource
{
    public static function make(Person $person): array
    {
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
