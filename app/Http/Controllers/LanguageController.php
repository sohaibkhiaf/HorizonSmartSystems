<?php

namespace App\Http\Controllers;

use App\Support\LocalizedUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    /**
     * Switch the current public language.
     */
    public function switch(
        Request $request,
        string $locale
    ): RedirectResponse {
        abort_unless(
            in_array(
                $locale,
                LocalizedUrl::locales(),
                true
            ),
            404
        );

        session([
            'locale' => $locale,
        ]);

        App::setLocale($locale);

        $redirect = trim(
            (string) $request->query(
                'redirect',
                ''
            )
        );

        if (
            !$this->isValidLocalizedRedirect(
                $redirect,
                $locale
            )
        ) {
            $redirect = LocalizedUrl::route(
                'home.index',
                [],
                $locale,
                [],
                false
            );
        }

        return redirect()
            ->to($redirect)
            ->withCookie(
                cookie(
                    'locale',
                    $locale,
                    60 * 24 * 30
                )
            );
    }

    /**
     * Prevent external redirect URLs.
     */
    private function isValidLocalizedRedirect(
        string $redirect,
        string $locale
    ): bool {
        if (
            $redirect === ''
            || !str_starts_with($redirect, '/')
            || str_starts_with($redirect, '//')
        ) {
            return false;
        }

        $parts = parse_url($redirect);

        if ($parts === false) {
            return false;
        }

        if (
            isset($parts['scheme'])
            || isset($parts['host'])
        ) {
            return false;
        }

        $path = rawurldecode(
            $parts['path'] ?? ''
        );

        return $path === '/' . $locale
            || str_starts_with(
                $path,
                '/' . $locale . '/'
            );
    }
}

