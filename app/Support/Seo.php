<?php

namespace App\Support;

use App\Models\PublicHero;
use App\Models\PublicService;
use App\Models\PublicSoftware;
use App\Models\StoreSubscription;
use Illuminate\Support\Str;

class Seo
{
    public static function htmlLang(): string
    {
        return LocalizedUrl::normalizeLocale();
    }

    public static function htmlDir(): string
    {
        return config('seo.locales.' . self::htmlLang() . '.dir', self::htmlLang() === 'ar' ? 'rtl' : 'ltr');
    }

    public static function forView(array $seo = [], array $sections = []): array
    {
        $robots = self::firstString(
            $sections['robots'] ?? null,
            $seo['robots'] ?? null,
            self::robotsForCurrentRequest()
        );

        $indexable = ! Str::contains(Str::lower($robots), 'noindex');

        $title = self::cleanText(self::firstString(
            $sections['seo_title'] ?? null,
            $seo['title'] ?? null,
            $sections['title'] ?? null,
            __('seo.default.title')
        ));

        $description = self::metaDescription(self::firstString(
            $sections['meta_description'] ?? null,
            $seo['description'] ?? null,
            $sections['description'] ?? null,
            __('seo.default.description')
        ));

        $canonical = self::firstString(
            $sections['canonical'] ?? null,
            $seo['canonical'] ?? null,
            LocalizedUrl::current()
        );

        $image = self::imageUrl(self::firstString(
            $sections['image'] ?? null,
            $sections['og_image'] ?? null,
            $seo['image'] ?? null,
            config('seo.default_image')
        ));

        $jsonLd = $seo['json_ld'] ?? $seo['jsonLd'] ?? [];

        if (! is_array($jsonLd) || self::isAssoc($jsonLd)) {
            $jsonLd = array_filter([$jsonLd]);
        }

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'robots' => $robots,
            'image' => $image,
            'type' => self::firstString($sections['og_type'] ?? null, $seo['type'] ?? null, 'website'),
            'locale' => self::ogLocale(self::htmlLang()),
            'alternate_og_locales' => self::alternateOgLocales(),
            'alternates' => $indexable ? ($seo['alternates'] ?? LocalizedUrl::currentAlternates()) : [],
            'json_ld' => $indexable ? array_values(array_filter($jsonLd)) : [],
            'indexable' => $indexable,
        ];
    }

    public static function home(?PublicHero $hero = null): array
    {
        return [
            'title' => __('seo.home.title'),
            'description' => __('seo.home.description'),
            'canonical' => LocalizedUrl::route('home.index'),
            'alternates' => LocalizedUrl::alternates('home.index'),
            'image' => $hero?->image ? 'uploads/' . $hero->image : config('seo.default_image'),
            'type' => 'website',
            'json_ld' => [
                self::organizationJsonLd($hero),
                self::websiteJsonLd(),
            ],
        ];
    }

    public static function service(PublicService $service, ?PublicHero $hero = null): array
    {
        $name = self::localizedModelText($service, 'name');
        $description = self::localizedModelText($service, 'short_description')
            ?: self::localizedModelText($service, 'description');

        return [
            'title' => __('seo.service.title', ['name' => $name]),
            'description' => $description,
            'canonical' => LocalizedUrl::route('services.show', ['service' => $service]),
            'alternates' => LocalizedUrl::alternates('services.show', ['service' => $service]),
            'image' => $service->image ? 'uploads/' . $service->image : config('seo.default_image'),
            'type' => 'website',
            'json_ld' => [
                self::serviceJsonLd($service),
                self::breadcrumbJsonLd([
                    ['name' => __('seo.breadcrumbs.home'), 'url' => LocalizedUrl::route('home.index')],
                    ['name' => $name, 'url' => LocalizedUrl::route('services.show', ['service' => $service])],
                ]),
            ],
        ];
    }

    public static function software(PublicSoftware $software, ?PublicHero $hero = null): array
    {
        $name = self::localizedModelText($software, 'name');
        $description = self::localizedModelText($software, 'short_description')
            ?: self::localizedModelText($software, 'description');

        return [
            'title' => __('seo.software.title', ['name' => $name]),
            'description' => $description,
            'canonical' => LocalizedUrl::route('software.show', ['software' => $software]),
            'alternates' => LocalizedUrl::alternates('software.show', ['software' => $software]),
            'image' => $software->image ? 'uploads/' . $software->image : config('seo.default_image'),
            'type' => 'website',
            'json_ld' => [
                self::softwareJsonLd($software),
                self::breadcrumbJsonLd([
                    ['name' => __('seo.breadcrumbs.home'), 'url' => LocalizedUrl::route('home.index')],
                    ['name' => $name, 'url' => LocalizedUrl::route('software.show', ['software' => $software])],
                ]),
            ],
        ];
    }

    public static function storeIndex(): array
    {
        /*
         * Store search, category, and pagination URLs are useful for users but thin for search.
         * They canonicalize to the localized main store page and are excluded from the sitemap.
         */
        return [
            'title' => __('seo.store.title'),
            'description' => __('seo.store.description'),
            'canonical' => LocalizedUrl::route('store.index'),
            'alternates' => LocalizedUrl::alternates('store.index'),
            'image' => 'images/store_hero.png',
            'type' => 'website',
            'json_ld' => [
                self::breadcrumbJsonLd([
                    ['name' => __('seo.breadcrumbs.home'), 'url' => LocalizedUrl::route('home.index')],
                    ['name' => __('seo.breadcrumbs.store'), 'url' => LocalizedUrl::route('store.index')],
                ]),
            ],
        ];
    }

    public static function privatePage(?string $title = null): array
    {
        return [
            'title' => $title ?: __('seo.default.title'),
            'description' => __('seo.default.description'),
            'robots' => 'noindex, nofollow',
            'canonical' => LocalizedUrl::current(),
            'alternates' => [],
            'json_ld' => [],
        ];
    }

    public static function robotsForCurrentRequest(): string
    {
        $request = request();

        if (! $request->isMethod('GET')) {
            return 'noindex, nofollow';
        }

        if (
            $request->routeIs('login.*')
            || $request->routeIs('admin.*')
            || $request->routeIs('customer.*')
            || $request->routeIs('store.order')
            || $request->routeIs('store.confirmed')
            || $request->is('admin') || $request->is('admin/*')
            || $request->is('customer') || $request->is('customer/*')
            || $request->is('password/*')
        ) {
            return 'noindex, nofollow';
        }

        return 'index, follow';
    }

    public static function organizationJsonLd(?PublicHero $hero = null): array
    {
        $sameAs = array_values(array_filter([
            $hero?->facebook_url,
            $hero?->instagram_url,
            $hero?->whatsapp_url,
            $hero?->linked_in_url,
        ]));

        return self::compact([
            '@context' => 'https://schema.org',
            '@type' => 'ProfessionalService',
            '@id' => LocalizedUrl::absolute('/#organization'),
            'name' => config('seo.site_name'),
            'alternateName' => config('seo.alternate_name'),
            'url' => LocalizedUrl::absolute('/'),
            'logo' => LocalizedUrl::asset(config('seo.logo')),
            'image' => LocalizedUrl::asset(config('seo.default_image')),
            'email' => config('seo.business.email'),
            'telephone' => config('seo.business.phone'),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => __('home.address_value'),
                'addressLocality' => config('seo.business.locality'),
                'addressCountry' => config('seo.business.country'),
            ],
            'areaServed' => [
                '@type' => 'Country',
                'name' => 'Algeria',
            ],
            'knowsAbout' => __('seo.business.services'),
            'sameAs' => $sameAs,
        ]);
    }

    public static function websiteJsonLd(): array
    {
        return self::compact([
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => LocalizedUrl::absolute('/#website'),
            'name' => config('seo.site_name'),
            'alternateName' => config('seo.alternate_name'),
            'url' => LocalizedUrl::absolute('/'),
            'inLanguage' => LocalizedUrl::locales(),
            'publisher' => [
                '@id' => LocalizedUrl::absolute('/#organization'),
            ],
        ]);
    }

    public static function serviceJsonLd(PublicService $service): array
    {
        $name = self::localizedModelText($service, 'name');
        $description = self::localizedModelText($service, 'description')
            ?: self::localizedModelText($service, 'short_description');

        return self::compact([
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $name,
            'description' => self::metaDescription($description),
            'serviceType' => $name,
            'provider' => [
                '@id' => LocalizedUrl::absolute('/#organization'),
            ],
            'areaServed' => [
                '@type' => 'Country',
                'name' => 'Algeria',
            ],
            'url' => LocalizedUrl::route('services.show', ['service' => $service]),
            'image' => $service->image ? LocalizedUrl::asset('uploads/' . $service->image) : null,
        ]);
    }

    public static function softwareJsonLd(PublicSoftware $software): array
    {
        $name = self::localizedModelText($software, 'name');
        $description = self::localizedModelText($software, 'description')
            ?: self::localizedModelText($software, 'short_description');

        return self::compact([
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => $name,
            'description' => self::metaDescription($description),
            'applicationCategory' => 'BusinessApplication',
            'publisher' => [
                '@id' => LocalizedUrl::absolute('/#organization'),
            ],
            'url' => LocalizedUrl::route('software.show', ['software' => $software]),
            'image' => $software->image ? LocalizedUrl::asset('uploads/' . $software->image) : null,
        ]);
    }

    public static function productJsonLd(StoreSubscription $subscription): ?array
    {
        $price = $subscription->discount > 0 ? $subscription->discount : $subscription->price;

        if (! $subscription->name || ! $subscription->image || ! $price) {
            return null;
        }

        return self::compact([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $subscription->name,
            'description' => $subscription->description,
            'image' => LocalizedUrl::asset('uploads/' . $subscription->image),
            'brand' => [
                '@type' => 'Brand',
                'name' => config('seo.site_name'),
            ],
            'offers' => [
                '@type' => 'Offer',
                'price' => $price,
                'priceCurrency' => 'DZD',
                'availability' => 'https://schema.org/InStock',
                'url' => LocalizedUrl::route('store.order', ['subscription' => $subscription]),
            ],
        ]);
    }

    public static function breadcrumbJsonLd(array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_values(array_map(function (array $item, int $index) {
                return self::compact([
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $item['name'] ?? '',
                    'item' => $item['url'] ?? null,
                ]);
            }, $items, array_keys($items))),
        ];
    }

    public static function localizedModelText(object $model, string $field, ?string $locale = null): string
    {
        $locale = LocalizedUrl::normalizeLocale($locale);
        $candidates = array_unique([
            $locale . '_' . $field,
            config('app.fallback_locale', 'fr') . '_' . $field,
            'fr_' . $field,
            'en_' . $field,
            'ar_' . $field,
        ]);

        foreach ($candidates as $candidate) {
            if (! empty($model->{$candidate})) {
                return self::cleanText($model->{$candidate});
            }
        }

        return '';
    }

    private static function ogLocale(string $locale): string
    {
        return config('seo.locales.' . $locale . '.og_locale', $locale);
    }

    private static function alternateOgLocales(): array
    {
        return array_values(array_filter(array_map(function (string $locale) {
            return $locale === self::htmlLang() ? null : self::ogLocale($locale);
        }, LocalizedUrl::locales())));
    }

    private static function imageUrl(?string $path): string
    {
        return LocalizedUrl::asset($path ?: config('seo.default_image'));
    }

    private static function metaDescription(?string $text): string
    {
        return Str::limit(self::cleanText($text), 165, '');
    }

    private static function cleanText(?string $text): string
    {
        $text = html_entity_decode(strip_tags((string) $text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text) ?: '';

        return trim($text);
    }

    private static function firstString(...$values): string
    {
        foreach ($values as $value) {
            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return '';
    }

    private static function compact(array $value): array
    {
        foreach ($value as $key => $item) {
            if (is_array($item)) {
                $item = self::compact($item);
            }

            if ($item === null || $item === '' || $item === []) {
                unset($value[$key]);
                continue;
            }

            $value[$key] = $item;
        }

        return $value;
    }

    private static function isAssoc(array $value): bool
    {
        return array_keys($value) !== range(0, count($value) - 1);
    }
}
