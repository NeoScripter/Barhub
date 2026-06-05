<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next, UserRole|string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        // Convert string role names to UserRole enums if needed
        $allowedRoles = array_map(
            fn(UserRole|string $role) => $role instanceof UserRole ? $role : UserRole::from((int) $role),
            $roles
        );

        abort_unless($user->hasAnyRole($allowedRoles), 403, 'У вас нет доступа к данной странице');

        return $next($request);
    }
}
