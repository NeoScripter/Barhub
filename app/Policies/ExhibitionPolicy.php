<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Exhibition;
use App\Models\User;

final class ExhibitionPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $user->exhibitions()->exists();
    }

    public function view(User $user, Exhibition $exhibition): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $exhibition->users()->where('user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function update(User $user): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function updateStatus(User $user, Exhibition $exhibition): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $exhibition->users()->where('user_id', $user->id)->exists();
    }

    public function delete(User $user): bool
    {
        return $this->isSuperAdmin($user);
    }
}
