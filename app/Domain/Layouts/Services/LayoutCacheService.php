<?php

declare(strict_types=1);

namespace App\Domain\Layouts\Services;

use App\Domain\Layouts\Models\Layout;
use Illuminate\Support\Facades\Cache;

class LayoutCacheService
{
    /**
     * Get cached layout HTML
     */
    public function get(Layout $layout, ?string $locale = null): ?string
    {
        $cacheKey = $this->cacheKey($layout, $locale);

        return Cache::get($cacheKey);
    }

    /**
     * Cache layout HTML
     */
    public function put(Layout $layout, string $html, ?string $locale = null): void
    {
        $cacheKey = $this->cacheKey($layout, $locale);
        $ttl = config('cache.layout_ttl', 3600); // 1 hour default

        Cache::put($cacheKey, $html, $ttl);
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
            Cache::forget($cacheKey);
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
}
