<?php
declare(strict_types=1);

namespace App\Actions;

use App\Enums\PersonRole;
use App\Models\Event;
use Illuminate\Support\Collection;

final class AttachRolesToPeople
{
    public function execute(Event|Collection $events): Collection
    {
        if ($events instanceof Event) {
            return $this->modifyEvent($events);
        }

        return $events->map(function ($event) {
            $event->setRelation('people', $this->modifyEvent($event));
            return $event;
        });
    }

    private function modifyEvent(Event $event): Collection
    {
        return $event->people->map(function ($person) {
            $person->role = PersonRole::from($person->pivot->role)->label();
            unset($person->pivot);
            return $person;
        });
    }
}
