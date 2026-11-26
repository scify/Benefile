<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * "role:2,5" on a route: only users with one of these roles, and administrators, go through.
 */
class RequireRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        abort_unless($user instanceof User && $user->isActive() && $user->hasRole(...array_map('intval', $roles)), 403);

        return $next($request);
    }
}
