<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureExhibitionAdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_if($user == null ||
            !$user->hasAnyRole([UserRole::SUPER_ADMIN, UserRole::ADMIN]), 403, 'Unauthorized');

        if ($user->role === UserRole::ADMIN) {
            abort_unless($user->exhibitions()->exists(), 403, 'Unauthorized');
        }

        return $next($request);
    }
}
