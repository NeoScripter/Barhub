<?php

declare(strict_types=1);

namespace App\Http\Resources\Integration;

use App\Models\Person;
use App\Services\Integration\HtmlSanitizer;

class PersonResource
{
    public static function make(Person $person): array
    {
        // На сайте имя хранится одной строкой в формате «Имя Фамилия».
        $fullName = preg_replace('/\s+/', ' ', trim((string) $person->name));
        $parts = explode(' ', $fullName, 2);

        $payload = [
            'id'          => $person->id,
            'firstName'   => $parts[0] !== '' ? $parts[0] : '—',
            'lastName'    => $parts[1] ?? '—',
            'description' => HtmlSanitizer::clean($person->regalia),
            'isSpeaker'   => true,
        ];

        // Без фото поле не отправляем — карточка остаётся без изображения.
        if ($person->avatar) {
            $payload['externalImagePath'] = route('integration.image', $person->avatar);
        }

        return $payload;
    }
}
