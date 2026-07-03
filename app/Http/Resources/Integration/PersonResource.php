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
        $name = explode(' ', $full_name);

        if (count($name) < 2) {
            $name[] = 'Неизвестно';
        }

        return [
            'id'                => $person->id,
            'firstName'          => $name[0],
            'lastName'          => $name[1],
            'description'          => $person->relalia,
            'isSpeaker'          => true,
            'position'          => 'Неизвестно',
            'company'             =>  'Неизвестно',
            'externalImagePath' => $person?->avatar->webp ?? url('placeholder.webp'),
        ];
    }
}
