<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LocalizedUrl
{
    public static function locales(): array
    {
        return array_keys(config('seo.locales', [
            'ar' => [],
            'fr' => [],
            'en' => [],
        ]));
    }

    public static function normalizeLocale(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        if (in_array($locale, self::locales(), true)) {
            return $locale;
        }

        $fallback = config('app.fallback_locale', 'fr');

        return in_array($fallback, self::locales(), true)
            ? $fallback
            : self::locales()[0];
    }

    public static function route(string $name, mixed $parameters = [], ?string $locale = null, array $query = []): string
    {
        $parameters = self::normalizeParameters($parameters);
        $path = route($name, $parameters, false);

        return self::absolute(self::appendQuery($path, array_merge($query, [
            'lang' => self::normalizeLocale($locale),
        ])));
    }

    public static function current(?string $locale = null, array $query = []): string
    {
        $route = request()->route();

        if ($route && $route->getName()) {
            return self::route($route->getName(), $route->parameters(), $locale, $query);
        }

        $path = '/' . ltrim(request()->path(), '/');

        return self::absolute(self::appendQuery($path, array_merge($query, [
            'lang' => self::normalizeLocale($locale),
        ])));
    }

    public static function alternates(string $name, mixed $parameters = [], array $query = []): array
    {
        $alternates = [];

        foreach (self::locales() as $locale) {
            $alternates[$locale] = self::route($name, $parameters, $locale, $query);
        }

        $xDefault = config('seo.x_default_locale', config('app.fallback_locale', 'fr'));
        $alternates['x-default'] = self::route($name, $parameters, self::normalizeLocale($xDefault), $query);

        return $alternates;
    }

    public static function currentAlternates(array $query = []): array
    {
        $route = request()->route();

        if ($route && $route->getName()) {
            return self::alternates($route->getName(), $route->parameters(), $query);
        }

        $alternates = [];

        foreach (self::locales() as $locale) {
            $alternates[$locale] = self::current($locale, $query);
        }

        $xDefault = config('seo.x_default_locale', config('app.fallback_locale', 'fr'));
        $alternates['x-default'] = self::current(self::normalizeLocale($xDefault), $query);

        return $alternates;
    }

    public static function asset(string $path): string
    {
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return self::absolute('/' . ltrim($path, '/'));
    }

    public static function absolute(string $path): string
    {
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $baseUrl = config('app.url') ?: 'https://hssdz.com';

        return rtrim((string) $baseUrl, '/') . '/' . ltrim($path, '/');
    }

    private static function appendQuery(string $path, array $query): string
    {
        $parts = parse_url($path);
        $existingQuery = [];

        if (! empty($parts['query'])) {
            parse_str($parts['query'], $existingQuery);
        }

        $query = array_filter(array_merge($existingQuery, $query), function ($value) {
            return $value !== null && $value !== '';
        });

        $base = $parts['path'] ?? '/';
        $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';

        if ($query === []) {
            return $base . $fragment;
        }

        return $base . '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986) . $fragment;
    }

    private static function normalizeParameters(mixed $parameters): array
    {
        if ($parameters instanceof Model) {
            return [$parameters];
        }

        if ($parameters === null || $parameters === '') {
            return [];
        }

        return is_array($parameters) ? $parameters : [$parameters];
    }
}
