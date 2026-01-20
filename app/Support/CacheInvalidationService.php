<?php

declare(strict_types=1);

namespace App\Support;

use App\Domain\Layouts\Models\Layout;
use App\Domain\Layouts\Services\LayoutCacheService;
use App\Domain\Modules\Models\ModuleInstance;
use Illuminate\Cache\TaggableStore;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;

class CacheInvalidationService
{
    /**
     * Register a page cache key for later invalidation.
     */
    public function registerPageCacheKey(string $cacheKey, ?int $businessId = null): void
    {
        if ($this->supportsTags()) {
            return; // Taggable stores don't need a registry.
        }

        $this->addToRegistry($this->registryKey($businessId), $cacheKey);
        $this->addToRegistry($this->registryKey(null), $cacheKey);
    }

    /**
     * Clear page cache (all or per business).
     */
    public function forgetPageCache(?int $businessId = null): void
    {
        if ($this->supportsTags()) {
            $tags = ['pages'];
            if ($businessId !== null) {
                $tags[] = "business:{$businessId}";
            }

            Cache::tags($tags)->flush();

            return;
        }

        $keys = $this->getRegistryKeys($businessId);
        foreach ($keys as $cacheKey) {
            Cache::forget($cacheKey);
        }

        $this->clearRegistry($businessId);
    }

    /**
     * Clear module cache for a specific module (uses previous timestamp if provided).
     */
    public function forgetModuleCache(ModuleInstance $module, ?int $previousUpdatedAt = null): void
    {
        $timestamp = $previousUpdatedAt ?? ($module->updated_at?->timestamp ?? now()->timestamp);
        $cacheKey = "module:{$module->id}:{$timestamp}";

        $cache = $this->cacheStore([
            'modules',
            "business:{$module->business_id}",
        ]);

        $cache->forget($cacheKey);
    }

    /**
     * Clear layout cache for a specific layout.
     */
    public function forgetLayoutCache(Layout $layout): void
    {
        app(LayoutCacheService::class)->forget($layout);
    }

    private function supportsTags(): bool
    {
        return Cache::getStore() instanceof TaggableStore;
    }

    /**
     * Get cache repository with tags if supported.
     */
    private function cacheStore(array $tags): Repository
    {
        if ($this->supportsTags()) {
            return Cache::tags($tags);
        }

        return Cache::store();
    }

    private function registryKey(?int $businessId): string
    {
        if ($businessId === null) {
            return 'page_cache_keys:all';
        }

        return "page_cache_keys:business:{$businessId}";
    }

    /**
     * Store registry as a keyed set to avoid duplicates.
     */
    private function addToRegistry(string $registryKey, string $cacheKey): void
    {
        $keys = Cache::get($registryKey, []);
        if (! is_array($keys)) {
            $keys = [];
        }

        $keys[$cacheKey] = true;

        Cache::put($registryKey, $keys, now()->addDays(30));
    }

    private function getRegistryKeys(?int $businessId = null): array
    {
        $registryKey = $this->registryKey($businessId);
        $keys = Cache::get($registryKey, []);

        if (! is_array($keys)) {
            return [];
        }

        return array_keys($keys);
    }

    private function clearRegistry(?int $businessId = null): void
    {
        Cache::forget($this->registryKey($businessId));
    }
}
