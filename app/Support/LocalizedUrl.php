<?php

namespace App\Support;

use App\Models\PublicService;
use App\Models\PublicSoftware;
use App\Models\StoreSubscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class LocalizedUrl
{
    /**
     * Routes that contain a model with localized slug columns.
     */
    private const LOCALIZED_MODEL_ROUTES = [
        'services.show' => [
            'parameter' => 'service',
            'model' => PublicService::class,
        ],

        'software.show' => [
            'parameter' => 'software',
            'model' => PublicSoftware::class,
        ],

        'store.order' => [
            'parameter' => 'subscription',
            'model' => StoreSubscription::class,
        ],

        'store.confirm' => [
            'parameter' => 'subscription',
            'model' => StoreSubscription::class,
        ],
    ];

    /**
     * Return supported locales.
     */
    public static function locales(): array
    {
        $locales = array_keys(
            config('seo.locales', [
                'ar' => [],
                'fr' => [],
                'en' => [],
            ])
        );

        return $locales !== []
            ? $locales
            : ['ar', 'fr', 'en'];
    }

    /**
     * Return a valid supported locale.
     */
    public static function normalizeLocale(
        ?string $locale = null
    ): string {
        $locale = $locale ?: app()->getLocale();

        if (in_array($locale, self::locales(), true)) {
            return $locale;
        }

        $fallback = config(
            'app.fallback_locale',
            'fr'
        );

        if (in_array($fallback, self::locales(), true)) {
            return $fallback;
        }

        return self::locales()[0] ?? 'fr';
    }

    /**
     * Strip the locale prefix from a route name.
     *
     * fr.services.show becomes services.show.
     */
    public static function logicalRouteName(
        ?string $routeName
    ): string {
        $routeName = trim((string) $routeName);

        if ($routeName === '') {
            return '';
        }

        foreach (self::locales() as $locale) {
            $prefix = $locale . '.';

            if (str_starts_with($routeName, $prefix)) {
                return substr(
                    $routeName,
                    strlen($prefix)
                );
            }
        }

        return $routeName;
    }

    /**
     * Return the real route name for a selected locale.
     *
     * services.show + fr becomes fr.services.show.
     */
    public static function localizedRouteName(
        string $routeName,
        ?string $locale = null
    ): string {
        $locale = self::normalizeLocale($locale);

        $logicalName = self::logicalRouteName(
            $routeName
        );

        $localizedName = $locale
            . '.'
            . $logicalName;

        if (Route::has($localizedName)) {
            return $localizedName;
        }

        /*
         * Global and admin routes are not localized.
         */
        if (Route::has($routeName)) {
            return $routeName;
        }

        if (Route::has($logicalName)) {
            return $logicalName;
        }

        throw new InvalidArgumentException(
            "Route [{$routeName}] is not defined."
        );
    }

    /**
     * Generate a localized route URL.
     *
     * Examples:
     *
     * LocalizedUrl::route('home.index')
     *
     * LocalizedUrl::route(
     *     'services.show',
     *     ['service' => $service]
     * )
     *
     * LocalizedUrl::route(
     *     'services.show',
     *     ['service' => $service],
     *     'ar'
     * )
     */
    public static function route(
        string $name,
        mixed $parameters = [],
        ?string $locale = null,
        array $query = [],
        bool $absolute = true
    ): string {
        $locale = self::normalizeLocale($locale);

        $logicalName = self::logicalRouteName($name);

        $actualRouteName = self::localizedRouteName(
            $name,
            $locale
        );

        $parameters = self::normalizeParameters(
            $parameters
        );

        $parameters = self::nameRouteParameters(
            $actualRouteName,
            $parameters
        );

        $parameters = self::localizeModelParameters(
            $logicalName,
            $parameters,
            $locale
        );

        $path = route(
            $actualRouteName,
            $parameters,
            false
        );

        $path = self::appendQuery(
            $path,
            $query
        );

        return $absolute
            ? self::absolute($path)
            : $path;
    }

    /**
     * Generate the current route in another language.
     */
    public static function current(
        ?string $locale = null,
        array $query = [],
        bool $absolute = true
    ): string {
        $locale = self::normalizeLocale($locale);

        $currentRoute = request()->route();

        if (
            $currentRoute !== null
            && $currentRoute->getName()
        ) {
            return self::route(
                $currentRoute->getName(),
                $currentRoute->parameters(),
                $locale,
                $query,
                $absolute
            );
        }

        /*
         * Fallback for unnamed routes.
         */
        $path = '/'
            . ltrim(request()->path(), '/');

        $path = self::replaceLocalePrefix(
            $path,
            $locale
        );

        $path = self::appendQuery(
            $path,
            $query
        );

        return $absolute
            ? self::absolute($path)
            : $path;
    }

    /**
     * Generate hreflang alternate URLs.
     */
    public static function alternates(
        string $name,
        mixed $parameters = [],
        array $query = []
    ): array {
        $alternates = [];

        foreach (self::locales() as $locale) {
            $alternates[$locale] = self::route(
                $name,
                $parameters,
                $locale,
                $query
            );
        }

        $xDefaultLocale = config(
            'seo.x_default_locale',
            config('app.fallback_locale', 'fr')
        );

        $alternates['x-default'] = self::route(
            $name,
            $parameters,
            self::normalizeLocale($xDefaultLocale),
            $query
        );

        return $alternates;
    }

    /**
     * Generate hreflang alternatives for the current route.
     */
    public static function currentAlternates(
        array $query = []
    ): array {
        $currentRoute = request()->route();

        if (
            $currentRoute !== null
            && $currentRoute->getName()
        ) {
            return self::alternates(
                $currentRoute->getName(),
                $currentRoute->parameters(),
                $query
            );
        }

        $alternates = [];

        foreach (self::locales() as $locale) {
            $alternates[$locale] = self::current(
                $locale,
                $query
            );
        }

        $xDefaultLocale = config(
            'seo.x_default_locale',
            config('app.fallback_locale', 'fr')
        );

        $alternates['x-default'] = self::current(
            self::normalizeLocale($xDefaultLocale),
            $query
        );

        return $alternates;
    }

    /**
     * Generate an absolute asset URL.
     */
    public static function asset(string $path): string
    {
        if (
            Str::startsWith(
                $path,
                ['http://', 'https://']
            )
        ) {
            return $path;
        }

        return self::absolute(
            '/' . ltrim($path, '/')
        );
    }

    /**
     * Convert a relative path into an absolute URL.
     */
    public static function absolute(string $path): string
    {
        if (
            Str::startsWith(
                $path,
                ['http://', 'https://']
            )
        ) {
            return $path;
        }

        $baseUrl = config('app.url')
            ?: 'https://hssdz.com';

        return rtrim(
            (string) $baseUrl,
            '/'
        ) . '/' . ltrim($path, '/');
    }

    /**
     * Replace models with their localized slug.
     */
    private static function localizeModelParameters(
        string $logicalRouteName,
        array $parameters,
        string $locale
    ): array {
        $configuration =
            self::LOCALIZED_MODEL_ROUTES[
                $logicalRouteName
            ] ?? null;

        if ($configuration === null) {
            return $parameters;
        }

        $parameterName =
            $configuration['parameter'];

        $modelClass =
            $configuration['model'];

        if (
            !array_key_exists(
                $parameterName,
                $parameters
            )
        ) {
            return $parameters;
        }

        $model = self::resolveModel(
            $parameters[$parameterName],
            $modelClass
        );

        if ($model === null) {
            return $parameters;
        }

        $slugColumn = $locale . '_slug';

        $slug = trim(
            (string) $model->getAttribute(
                $slugColumn
            )
        );

        if ($slug === '') {
            throw new RuntimeException(
                "The model [{$modelClass}] with ID "
                . "[{$model->getKey()}] has no "
                . "[{$slugColumn}] value."
            );
        }

        $parameters[$parameterName] = $slug;

        return $parameters;
    }

    /**
     * Resolve a model from a model, ID, or localized slug.
     */
    private static function resolveModel(
        mixed $value,
        string $modelClass
    ): ?Model {
        if ($value instanceof $modelClass) {
            return $value;
        }

        if ($value instanceof Model) {
            return null;
        }

        if (is_int($value)) {
            return $modelClass::query()->find($value);
        }

        if (!is_scalar($value)) {
            return null;
        }

        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        /*
         * Search the active locale first.
         */
        $currentLocale = self::normalizeLocale(
            app()->getLocale()
        );

        $model = $modelClass::query()
            ->where(
                $currentLocale . '_slug',
                $value
            )
            ->first();

        if ($model !== null) {
            return $model;
        }

        /*
         * Search all slug columns.
         */
        $model = $modelClass::query()
            ->where(function ($query) use ($value) {
                foreach (
                    self::locales() as $index => $locale
                ) {
                    if ($index === 0) {
                        $query->where(
                            $locale . '_slug',
                            $value
                        );
                    } else {
                        $query->orWhere(
                            $locale . '_slug',
                            $value
                        );
                    }
                }
            })
            ->first();

        if ($model !== null) {
            return $model;
        }

        /*
         * Allow numeric IDs for internal calls.
         */
        if (ctype_digit($value)) {
            return $modelClass::query()->find(
                (int) $value
            );
        }

        return null;
    }

    /**
     * Convert positional route parameters into named parameters.
     */
    private static function nameRouteParameters(
        string $routeName,
        array $parameters
    ): array {
        $numericParameters = [];
        $namedParameters = [];

        foreach ($parameters as $key => $value) {
            if (is_int($key)) {
                $numericParameters[] = $value;
            } else {
                $namedParameters[$key] = $value;
            }
        }

        if ($numericParameters === []) {
            return $namedParameters;
        }

        $parameterNames = self::routeParameterNames(
            $routeName
        );

        $availableNames = array_values(
            array_filter(
                $parameterNames,
                fn (string $parameterName) =>
                    !array_key_exists(
                        $parameterName,
                        $namedParameters
                    )
            )
        );

        foreach (
            $numericParameters as $index => $value
        ) {
            $parameterName =
                $availableNames[$index] ?? null;

            if ($parameterName !== null) {
                $namedParameters[$parameterName] =
                    $value;
            } else {
                $namedParameters[] = $value;
            }
        }

        return $namedParameters;
    }

    /**
     * Return a route's parameter names.
     */
    private static function routeParameterNames(
        string $routeName
    ): array {
        $route = Route::getRoutes()
            ->getByName($routeName);

        return $route
            ? $route->parameterNames()
            : [];
    }

    /**
     * Normalize route parameters.
     */
    private static function normalizeParameters(
        mixed $parameters
    ): array {
        if ($parameters instanceof Model) {
            return [$parameters];
        }

        if (
            $parameters === null
            || $parameters === ''
        ) {
            return [];
        }

        return is_array($parameters)
            ? $parameters
            : [$parameters];
    }

    /**
     * Append query parameters to a URL.
     */
    private static function appendQuery(
        string $path,
        array $query
    ): string {
        $parts = parse_url($path);

        $existingQuery = [];

        if (!empty($parts['query'])) {
            parse_str(
                $parts['query'],
                $existingQuery
            );
        }

        $query = array_filter(
            array_merge(
                $existingQuery,
                $query
            ),
            fn ($value) =>
                $value !== null
                && $value !== ''
        );

        $base = $parts['path'] ?? '/';

        $fragment = isset($parts['fragment'])
            ? '#' . $parts['fragment']
            : '';

        if ($query === []) {
            return $base . $fragment;
        }

        return $base
            . '?'
            . http_build_query(
                $query,
                '',
                '&',
                PHP_QUERY_RFC3986
            )
            . $fragment;
    }

    /**
     * Replace the locale prefix on an unnamed URL.
     */
    private static function replaceLocalePrefix(
        string $path,
        string $locale
    ): string {
        $segments = array_values(
            array_filter(
                explode(
                    '/',
                    trim($path, '/')
                ),
                fn ($segment) => $segment !== ''
            )
        );

        if (
            isset($segments[0])
            && in_array(
                $segments[0],
                self::locales(),
                true
            )
        ) {
            $segments[0] = $locale;
        } else {
            array_unshift(
                $segments,
                $locale
            );
        }

        return '/' . implode('/', $segments);
    }
}

