<?php

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
            static::SUPER_ADMIN => 'Руководитель',
            static::ADMIN => 'Администратор',
            static::EXPONENT => 'Экспонент',
            static::USER => 'Пользователь',
        };
    }
}
