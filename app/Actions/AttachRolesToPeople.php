<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PersonRole;
use App\Models\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class AttachRolesToPeople
{
    public function execute(Event|Collection $events): array
    {
        if ($events instanceof Event) {
            return $this->modifyEvent($events);
        }

        return $events->map(
            fn(Event $event) =>
            $this->modifyEvent($event)
        )->toArray();
    }

    private function modifyEvent(Event $event): array
    {
        $people = $event
            ->people
            ->groupBy('name')
            ->map(
                fn($group) =>
                [
                    'id'      => $group[0]->id,
                    'name'    => $group[0]->name,
                    'role'    => mb_strtolower(
                        implode(
                            ', ',
                            $group->map(
                                fn($person) =>
                                PersonRole::tryFrom($person->pivot->role) // ← tryFrom instead of from
                                    ?->label() ?? $person->pivot->role    // ← fallback if null/invalid
                            )->filter()->toArray()
                        )
                    ),
                    'avatar'  => ($group[0]->avatar instanceof Model)
                        ? $group[0]->avatar->toArray()
                        : null,
                    'logo'    => ($group[0]->logo instanceof Model)
                        ? $group[0]->logo->toArray()
                        : null,
                    'regalia' => $group[0]->regalia,
                    'bio'     => $group[0]->bio,
                ]
            )->values()->toArray();

        $event = $event->toArray();
        $event['people'] = $people;

        return $event;
    }
}
