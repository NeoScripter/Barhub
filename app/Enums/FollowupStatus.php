<?php

declare(strict_types=1);

namespace App\Enums;

enum FollowupStatus: int
{
    case COMPLETED = 1;
    case INCOMPLETE = 2;

    public function label(): string
    {
        return match ($this) {
            self::COMPLETED => 'Закрыт',
            self::INCOMPLETE => 'Открыт',
        };
    }
}
