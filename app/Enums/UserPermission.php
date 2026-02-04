<?php

namespace App\Enums;

enum UserPermission: string
{
    case MANAGE_EXHIBITIONS = 'manage-exhibitions';
    case MANAGE_EXHIBITION = 'manage-exhibition';
    case ACCESS_ADMIN_PANEL = 'access-admin-panel';
}
