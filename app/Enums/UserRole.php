<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    // case NAMEINAPP = 'name-in-database';

    case SUPER_ADMIN = 'super-admin';
    case ADMIN = 'admin';
    case EXPONENT = 'exponent';
    case USER = 'user';

    // extra helper to allow for greater customization of displayed values, without disclosing the name/value data directly
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
