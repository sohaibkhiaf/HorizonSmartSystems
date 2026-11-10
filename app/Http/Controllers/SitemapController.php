<?php

namespace App\Http\Controllers;

use App\Models\PublicAchievement;
use App\Models\PublicHero;
use App\Models\PublicService;
use App\Models\PublicSoftware;
use App\Models\StoreSubscription;
use App\Support\LocalizedUrl;
use Illuminate\Support\Carbon;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $urls = collect()
            ->merge($this->localizedEntries('home.index', [], $this->homeLastModified()))
            ->merge($this->localizedEntries('store.index', [], $this->storeLastModified()))
            ->merge($this->serviceEntries())
            ->merge($this->softwareEntries())
            ->unique('loc')
            ->values()
            ->all();

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    private function serviceEntries(): array
    {
        return PublicService::where('is_published', true)
            ->latest('updated_at')
            ->get()
            ->flatMap(function (PublicService $service) {
                return $this->localizedEntries('services.show', ['service' => $service], $service->updated_at);
            })
            ->all();
    }

    private function softwareEntries(): array
    {
        return PublicSoftware::where('is_published', true)
            ->latest('updated_at')
            ->get()
            ->flatMap(function (PublicSoftware $software) {
                return $this->localizedEntries('software.show', ['software' => $software], $software->updated_at);
            })
            ->all();
    }

    private function localizedEntries(string $routeName, array $parameters = [], mixed $lastModified = null): array
    {
        $alternates = LocalizedUrl::alternates($routeName, $parameters);
        $lastmod = $this->formatLastModified($lastModified);

        return collect(LocalizedUrl::locales())
            ->map(function (string $locale) use ($routeName, $parameters, $alternates, $lastmod) {
                return [
                    'loc' => LocalizedUrl::route($routeName, $parameters, $locale),
                    'lastmod' => $lastmod,
                    'alternates' => $alternates,
                ];
            })
            ->all();
    }

    private function homeLastModified(): ?string
    {
        return collect([
            PublicHero::query()->max('updated_at'),
            PublicService::where('is_published', true)->max('updated_at'),
            PublicSoftware::where('is_published', true)->max('updated_at'),
            PublicAchievement::where('is_published', true)->max('updated_at'),
        ])->filter()->max();
    }

    private function storeLastModified(): ?string
    {
        return StoreSubscription::where('is_published', true)->max('updated_at');
    }

    private function formatLastModified(mixed $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        return $value instanceof Carbon
            ? $value->toAtomString()
            : Carbon::parse($value)->toAtomString();
    }
}
