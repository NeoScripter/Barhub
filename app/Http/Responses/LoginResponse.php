<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Enums\UserRole;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

final class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = auth()->user();

        if ($user->hasRole(UserRole::SUPER_ADMIN) || $user->hasRole(UserRole::ADMIN)) {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($user->hasRole(UserRole::EXPONENT)) {
            return redirect()->intended(route('exponent.dashboard'));
        }

        return redirect()->intended(route('/'));
    }
}
