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
    case PARTNER = 6;

    public static function toSelectList(): array
    {
        return array_map(
            fn (self $role): array => [
                'value' => $role->value,
                'label' => $role->label(),
            ],
            self::cases()
        );
    }

    public function label(): string
    {
        return match ($this) {
            self::SPEAKER => 'Спикер',
            self::ORGANIZER => 'Организатор',
            self::CURATOR => 'Куратор',
            self::HOST => 'Ведущий',
            self::RESIDENT => 'Резидент',
            self::PARTNER => 'Партнер',
        };
    }
}
