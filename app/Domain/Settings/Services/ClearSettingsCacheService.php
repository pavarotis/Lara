<?php

declare(strict_types=1);

namespace App\Domain\Settings\Services;

use Illuminate\Support\Facades\Cache;

class ClearSettingsCacheService
{
    /**
     * Check if cache store supports tagging
     */
    private function supportsTagging(): bool
    {
        $store = Cache::getStore();

        return method_exists($store, 'tags');
    }

    /**
     * Clear cache for a specific setting
     */
    public function execute(?string $key = null): void
    {
        if ($key) {
            Cache::forget("settings:{$key}");
        }

        // Clear group and all settings cache
        Cache::forget('settings:all');

        // Clear group caches
        $groups = \App\Domain\Settings\Models\Setting::distinct()->pluck('group');
        foreach ($groups as $group) {
            Cache::forget("settings:group:{$group}");
        }

        // If tagging is supported, flush tags
        if ($this->supportsTagging()) {
            try {
                Cache::tags(['settings'])->flush();
            } catch (\Exception $e) {
                // Ignore if tagging fails
            }
        }
    }

    /**
     * Clear all settings cache
     */
    public function clearAll(): void
    {
        Cache::forget('settings:all');

        // Clear group caches
        $groups = \App\Domain\Settings\Models\Setting::distinct()->pluck('group');
        foreach ($groups as $group) {
            Cache::forget("settings:group:{$group}");
        }

        // If tagging is supported, flush tags
        if ($this->supportsTagging()) {
            try {
                Cache::tags(['settings'])->flush();
            } catch (\Exception $e) {
                // Ignore if tagging fails
            }
        }
    }

    /**
     * Clear cache for a settings group
     */
    public function clearGroup(string $group): void
    {
        Cache::forget("settings:group:{$group}");
        Cache::forget('settings:all');
    }
}
