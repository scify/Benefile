<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lets only active administrators through. Use it after the "activated" middleware.
 */
class IsAdministrator
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user instanceof User && $user->isAdmin() && $user->isActive()) {
            return $next($request);
        }

        abort(403);
    }
}
