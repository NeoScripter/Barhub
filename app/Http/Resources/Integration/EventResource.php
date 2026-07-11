<?php

declare(strict_types=1);

namespace App\Http\Resources\Integration;

use App\Models\Event;
use App\Services\Integration\HtmlSanitizer;

class EventResource
{
    public static function make(Event $event): array
    {
        // В БД время хранится в UTC, а Eventicious ждёт локальное время
        // мероприятия — конвертируем (по умолчанию Москва).
        $timezone = config('services.eventicious.timezone', 'Europe/Moscow');

        return [
            'id'           => $event->id,
            'language'     => 'ru-RU',
            'title'        => $event->title,
            'description'  => HtmlSanitizer::clean($event->description),
            'startTime'    => $event->starts_at->timezone($timezone)->format('Y-m-d\TH:i'),
            'endTime'      => $event->ends_at->timezone($timezone)->format('Y-m-d\TH:i'),
            'type'         => 0,
            'locationsIds' => $event->stage_id ? [$event->stage_id] : [],
            'speakersIds'  => $event->people()->pluck('people.id')->unique()->values()->all(),
            'tagIds'       => $event->themes()->pluck('themes.id')->all(),
            'aclGroupsIds' => [],
        ];
    }
}
