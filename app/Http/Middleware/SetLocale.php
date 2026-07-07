<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = ['ar', 'fr', 'en'];
        $queryLocale = $request->query('lang');

        if (in_array($queryLocale, $supportedLocales, true)) {
            $locale = $queryLocale;
            session(['locale' => $locale]);
        } else {
            $locale = session('locale')
                ?? $request->cookie('locale')
                ?? config('app.locale');
        }

        if (! in_array($locale, $supportedLocales, true)) {
            $locale = config('app.fallback_locale', 'fr');
        }

        App::setLocale($locale);

        $response = $next($request);

        if (in_array($queryLocale, $supportedLocales, true)) {
            $response->headers->setCookie(cookie('locale', $locale, 60 * 24 * 30));
        }

        return $response;
    }
}
