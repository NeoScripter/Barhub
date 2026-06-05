<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

final class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->hasRole(UserRole::SUPER_ADMIN) || $user->hasRole(UserRole::ADMIN)) {
            return redirect()->intended(route('admin.dashboard.index'));
        }

        if ($user->hasRole(UserRole::EXPONENT)) {
            return redirect()->intended(route('exponent.tasks.index'));
        }

        return redirect()->intended('/exhibitions/1/events');
    }
}
