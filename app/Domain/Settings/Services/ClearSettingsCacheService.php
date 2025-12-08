<?php

declare(strict_types=1);

namespace App\Domain\Settings\Services;

use Illuminate\Support\Facades\Cache;

class ClearSettingsCacheService
{
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
        Cache::tags(['settings'])->flush();
    }

    /**
     * Clear all settings cache
     */
    public function clearAll(): void
    {
        Cache::forget('settings:all');
        Cache::tags(['settings'])->flush();
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
