<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lets only logged-in users with an active account through.
 *
 * A new account waits for an administrator to activate it. A deactivated
 * account has no access, even if it was activated before.
 */
class IsEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user instanceof User) {
            return $request->expectsJson() ? response('Unauthorized.', 401) : redirect()->guest(url('auth/login'));
        }

        if ($user->isActive()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response('Forbidden.', 403);
        }

        return redirect($user->is_deactivated === 1 ? 'disabledUser' : 'awaitingActivation');
    }
}
