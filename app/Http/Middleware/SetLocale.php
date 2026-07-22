<?php

namespace App\Http\Middleware;

use App\Support\LocalizedUrl;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $supportedLocales =
            LocalizedUrl::locales();

        /*
         * Localized public route names:
         *
         * ar.services.show
         * fr.services.show
         * en.services.show
         */
        $routeName = (string) (
            $request->route()?->getName() ?? ''
        );

        $routeLocale = null;

        foreach ($supportedLocales as $supportedLocale) {
            if (
                str_starts_with(
                    $routeName,
                    $supportedLocale . '.'
                )
            ) {
                $routeLocale = $supportedLocale;

                break;
            }
        }

        /*
         * Keep support for old ?lang=fr links temporarily.
         */
        $queryLocale = $request->query('lang');

        if (
            !in_array(
                $queryLocale,
                $supportedLocales,
                true
            )
        ) {
            $queryLocale = null;
        }

        /*
         * URL language has the highest priority.
         */
        $locale = $routeLocale
            ?? $queryLocale
            ?? session('locale')
            ?? $request->cookie('locale')
            ?? config('app.locale', 'fr');

        if (
            !in_array(
                $locale,
                $supportedLocales,
                true
            )
        ) {
            $locale = config(
                'app.fallback_locale',
                'fr'
            );
        }

        if (
            !in_array(
                $locale,
                $supportedLocales,
                true
            )
        ) {
            $locale = 'fr';
        }

        App::setLocale($locale);

        /*
         * Remember the language selected through the URL.
         */
        if (
            $routeLocale !== null
            || $queryLocale !== null
        ) {
            session([
                'locale' => $locale,
            ]);
        }

        $response = $next($request);

        /*
         * Store the selected locale for 30 days.
         */
        if (
            $routeLocale !== null
            || $queryLocale !== null
        ) {
            $response->headers->setCookie(
                cookie(
                    'locale',
                    $locale,
                    60 * 24 * 30
                )
            );
        }

        return $response;
    }
}

