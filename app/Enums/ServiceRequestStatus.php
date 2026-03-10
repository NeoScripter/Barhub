<?php

declare(strict_types=1);

namespace App\Enums;

enum ServiceRequestStatus: int
{
    case COMPLETED = 1;
    case IMCOMPLETE = 2;

    public function label(): string
    {
        return match ($this) {
            self::COMPLETED => 'Закрыт',
            self::IMCOMPLETE => 'Открыт',
        };
    }
}
