<?php

declare(strict_types=1);

namespace App\Enums;

enum PersonRole: int
{
    case SPEAKER = 1;
    case ORGANIZER = 2;
    case CURATOR = 3;
    case HOST = 4;
    case RESIDENT = 5;

    public function label(): string
    {
        return match ($this) {
            self::SPEAKER => 'спикер',
            self::ORGANIZER => 'организатор',
            self::CURATOR => 'куратор',
            self::HOST => 'ведущий',
            self::RESIDENT => 'резидент',
        };
    }

    public static function toSelectList(): array
    {
        return array_map(
            fn(self $role) => [
                'value' => $role->value,
                'label' => $role->label(),
            ],
            self::cases()
        );
    }
}
