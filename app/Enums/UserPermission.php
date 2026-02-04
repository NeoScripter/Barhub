<?php

namespace App\Enums;

enum UserPermission: string
{
    case MANAGE_EXHIBITIONS = 'manage-exhibitions';
    case ACCESS_ADMIN_PANEL = 'access-admin-panel';
}
