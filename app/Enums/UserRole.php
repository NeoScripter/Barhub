<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: int
{
    case SUPER_ADMIN = 1;
    case ADMIN = 2;
    case EXPONENT = 3;
    case USER = 4;

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Руководитель',
            self::ADMIN => 'Администратор',
            self::EXPONENT => 'Экспонент',
            self::USER => 'Пользователь',
        };
    }
}
