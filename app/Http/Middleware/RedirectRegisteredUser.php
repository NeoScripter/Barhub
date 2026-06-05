<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class RedirectRegisteredUser
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->hasRole(UserRole::SUPER_ADMIN) || $user->hasRole(UserRole::ADMIN)) {
                return redirect()->route('admin.dashboard.index');
            }

            if ($user->hasRole(UserRole::EXPONENT)) {
                return redirect()->intended('exponent.tasks.index');
            }
        }

        return $next($request);
    }
}
