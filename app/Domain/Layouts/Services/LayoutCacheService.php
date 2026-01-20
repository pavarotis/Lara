<?php

declare(strict_types=1);

namespace App\Domain\Layouts\Services;

use App\Domain\Layouts\Models\Layout;
use Illuminate\Cache\TaggableStore;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;

class LayoutCacheService
{
    /**
     * Get cached layout HTML
     */
    public function get(Layout $layout, ?string $locale = null): ?string
    {
        $cacheKey = $this->cacheKey($layout, $locale);

        return $this->cacheStore($layout->business_id)->get($cacheKey);
    }

    /**
     * Cache layout HTML
     */
    public function put(Layout $layout, string $html, ?string $locale = null): void
    {
        $cacheKey = $this->cacheKey($layout, $locale);
        $ttl = config('cache.layout_ttl', 3600); // 1 hour default

        $this->cacheStore($layout->business_id)->put($cacheKey, $html, $ttl);
    }

    /**
     * Invalidate cache
     */
    public function forget(Layout $layout): void
    {
        // Invalidate for all locales
        $locales = config('app.available_locales', ['en']);

        foreach ($locales as $locale) {
            $cacheKey = $this->cacheKey($layout, $locale);
            $this->cacheStore($layout->business_id)->forget($cacheKey);
        }
    }

    /**
     * Generate cache key
     */
    private function cacheKey(Layout $layout, ?string $locale): string
    {
        $locale = $locale ?? app()->getLocale();

        return "layout:{$layout->id}:{$locale}:{$layout->updated_at->timestamp}";
    }

    private function cacheStore(int $businessId): Repository
    {
        if (Cache::getStore() instanceof TaggableStore) {
            return Cache::tags(['layouts', "business:{$businessId}"]);
        }

        return Cache::store();
    }
}
