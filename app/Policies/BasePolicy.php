<?php

declare(strict_types=1);

// app/Policies/BasePolicy.php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

abstract class BasePolicy
{
    /**
     * Check if user is super admin.
     */
    protected function isSuperAdmin(User $user): bool
    {
        return $user->role === UserRole::SUPER_ADMIN;
    }

    /**
     * Check if user is admin (or super admin).
     */
    protected function isAdmin(User $user): bool
    {
        return $user->role === UserRole::ADMIN;
    }
}
