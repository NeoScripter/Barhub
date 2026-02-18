<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PersonRole;
use App\Models\Person;
use Illuminate\Support\Collection;

final class AttachRolesToEvents
{
    public function execute(Person|Collection $people): Collection
    {
        if ($people instanceof Person) {
            return $this->modifyEvents($people);
        }

        return $people->map(function (Person $person): Person {
            $person->setRelation('events', $this->modifyEvents($person));

            return $person;
        });
    }

    private function modifyEvents(Person $person): Collection
    {
        return $person->events
            ->groupBy('id')
            ->map(function ($eventGroup) {
                $event = $eventGroup->first();
                $event->roles = $eventGroup
                    ->pluck('pivot.role')
                    ->map(fn ($role) => PersonRole::from($role))->label()
                    ->values()
                    ->all();
                unset($event->pivot);

                return $event;
            })
            ->values();
    }
}
