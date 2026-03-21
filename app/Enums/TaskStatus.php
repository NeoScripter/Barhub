<?php

declare(strict_types=1);

namespace App\Enums;

enum TaskStatus: int
{
    case COMPLETED = 1;
    case TO_BE_COMPLETED = 2;
    case TO_BE_VERIFIED = 3;
    case INCOMPLETE = 4;
    case DELAYED = 5;

    public function label(): string
    {
        return match ($this) {
            self::COMPLETED => 'Выполнена',
            self::TO_BE_COMPLETED => 'Ожидает выполнения',
            self::TO_BE_VERIFIED => 'На проверке',
            self::INCOMPLETE => 'Требует доработки',
            self::DELAYED => 'Просрочена',
        };
    }
}
