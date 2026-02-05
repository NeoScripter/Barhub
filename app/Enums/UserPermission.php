<?php

declare(strict_types=1);

namespace App\Enums;

enum UserPermission: string
{
    case MANAGE_EXHIBITIONS = 'manage-exhibitions';
    case VIEW_EXHIBITIONS = 'view-exhibitions';
    case ACCESS_ADMIN_PANEL = 'access-admin-panel';
}
