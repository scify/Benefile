<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sets the language of the interface from the choice of the visitor (in the session).
 *
 * Without a choice, the language is the default of the application (APP_LOCALE).
 */
class SetLocale
{
    /** The languages of the interface: the code of the language files => the code of the HTML lang attribute. */
    public const LANGUAGES = ['gr' => 'el', 'en' => 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->hasSession() ? $request->session()->get('locale') : null;
        if (is_string($locale) && array_key_exists($locale, self::LANGUAGES)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
