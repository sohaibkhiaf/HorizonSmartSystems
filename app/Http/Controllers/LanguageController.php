<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switch($locale) {

        // switch
        if (in_array($locale, ['en', 'fr', 'ar'])) {

            session(['locale' => $locale]);

            // back + cookie
            return redirect()->to($this->localizedPreviousUrl($locale))
                ->withCookie(cookie('locale', $locale, 60 * 24 * 30));
        }

        // back
        return redirect()->back();
    }

    private function localizedPreviousUrl(string $locale): string
    {
        $previous = url()->previous();
        $parts = parse_url($previous);
        $query = [];

        if (! empty($parts['query'])) {
            parse_str($parts['query'], $query);
        }

        $query['lang'] = $locale;

        $scheme = isset($parts['scheme']) ? $parts['scheme'] . '://' : '';
        $host = $parts['host'] ?? '';
        $port = isset($parts['port']) ? ':' . $parts['port'] : '';
        $path = $parts['path'] ?? '/';
        $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';

        if (str_starts_with($path, '/language/')) {
            $path = '/';
        }

        return $scheme . $host . $port . $path . '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986) . $fragment;
    }
}
