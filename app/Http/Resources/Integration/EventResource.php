<?php

declare(strict_types=1);

namespace App\Http\Resources\Integration;

use App\Models\Event;
use Illuminate\Support\Facades\DB;

class EventResource
{
    public static function make(Event $event): array
    {
        $speaker_ids = DB::select(
            'SELECT p.id FROM people p
            JOIN event_person
            ON event_id = ?
            AND person_id = p.id',
            [$event->id]
        );

        $speaker_ids = array_map(
            fn($arr) => $arr->id,
            $speaker_ids
        );

        $tag_ids = $event->themes()->pluck('id')->toArray();

        return [
            'id'                => $event->id,
            'language'          => 'ru-RU',
            'title'             => $event->title,
            'description'       => $event->description,
            'startTime'         => $event->starts_at,
            'endTime'           => $event->ends_at,
            'type'              => 0,
            "locationsIds" => [
                5
            ],
            'aclGroupsIds'      => [],
            'externalImagePath' => url('placeholder.webp'),
            'speakerIds'        => $speaker_ids,
            'tagIds'            => $tag_ids,
        ];
    }
}
