<?php
declare(strict_types=1);

namespace App\Actions;

use App\Models\Event;
use App\Models\Person;
use Illuminate\Database\Eloquent\Collection;

final class FormatEventPeople
{
    /**
     * @return array<int, array{person_id: int, name: string, roles: array<int, int>}>
     */
    public function execute(Event $event): array
    {
        /** @var Collection<int, Person> $people */
        $people = $event->people;

        // Group by person_id since same person can have multiple roles
        return $people
            ->groupBy('id')
            ->map(function (Collection $personGroup): array {
                /** @var Person $firstPerson */
                $firstPerson = $personGroup->first();

                return [
                    'person_id' => $firstPerson->id,
                    'name' => $firstPerson->name,
                    'roles' => $personGroup->map(function (Person $person): int {
                        /** @var object{role: int} $pivot */
                        $pivot = $person->pivot;
                        return $pivot->role;
                    })->values()->toArray(),
                ];
            })
            ->values()
            ->toArray();
    }
}
