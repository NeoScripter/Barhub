<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Event;
use App\Models\Person;
use Illuminate\Database\Eloquent\Collection;

final class FormatEventPeople
{
    /**
     * @return array<int, array{person_id: int, name: string, role: int}>
     */
    public function execute(Event $event): array
    {
        /** @var Collection<int, Person> $people */
        $people = $event->people;

        return $people->map(function (Person $person): array {
            /** @var object{role: int} $pivot */
            $pivot = $person->pivot;

            return [
                'person_id' => $person->id,
                'name' => $person->name,
                'role' => $pivot->role,
            ];
        })->toArray();
    }
}
