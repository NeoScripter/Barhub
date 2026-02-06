<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  UserRole|string  ...$roles
     */
    public function handle(Request $request, Closure $next, UserRole|string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // Convert string role names to UserRole enums if needed
        $allowedRoles = array_map(
            fn($role) => $role instanceof UserRole ? $role : UserRole::from((int) $role),
            $roles
        );

        if (!$user->hasAnyRole($allowedRoles)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
