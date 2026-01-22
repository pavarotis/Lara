<?php

declare(strict_types=1);

namespace App\Domain\Settings\Services;

use App\Domain\Settings\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class GetSettingsService
{
    /**
     * Check if settings table exists
     */
    private function tableExists(): bool
    {
        return Schema::hasTable('settings');
    }

    /**
     * Check if cache store supports tagging
     */
    private function supportsTagging(): bool
    {
        $store = Cache::getStore();

        return method_exists($store, 'tags');
    }

    /**
     * Get cache instance (with or without tags)
     */
    private function getCache()
    {
        if ($this->supportsTagging()) {
            return Cache::tags(['settings']);
        }

        return Cache::store();
    }

    /**
     * Get all settings (cached)
     */
    public function all(): array
    {
        if (! $this->tableExists()) {
            return [];
        }

        $cache = $this->getCache();

        return $cache->remember('settings:all', now()->addHour(), function () {
            try {
                return Setting::all()
                    ->mapWithKeys(fn ($setting) => [$setting->key => $this->castValue($setting)])
                    ->toArray();
            } catch (\Exception $e) {
                return [];
            }
        });
    }

    /**
     * Get a specific setting
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->all();

        return $settings[$key] ?? $default;
    }

    /**
     * Get settings by group
     */
    public function getGroup(string $group): array
    {
        if (! $this->tableExists()) {
            return [];
        }

        $cache = $this->getCache();

        return $cache->remember("settings:group:{$group}", now()->addHour(), function () use ($group) {
            try {
                return Setting::group($group)
                    ->get()
                    ->mapWithKeys(fn ($setting) => [$setting->key => $this->castValue($setting)])
                    ->toArray();
            } catch (\Exception $e) {
                return [];
            }
        });
    }

    /**
     * Cast value based on type
     */
    private function castValue(Setting $setting): mixed
    {
        // Value is stored as string in DB, cast based on type
        // Use getRawOriginal to get the actual DB value (not casted)
        $rawValue = $setting->getRawOriginal('value');
        if ($rawValue === null) {
            $rawValue = $setting->value; // Fallback if getRawOriginal fails
        }

        return match ($setting->type) {
            'boolean' => filter_var($rawValue, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $rawValue,
            'decimal', 'float' => (float) $rawValue,
            'json' => is_string($rawValue) ? json_decode($rawValue, true) : $rawValue,
            default => $rawValue,
        };
    }

    /**
     * Clear settings cache
     */
    public function clearCache(): void
    {
        Cache::forget('settings:all');

        // Clear group caches
        if ($this->tableExists()) {
            try {
                $groups = Setting::distinct()->pluck('group');
                foreach ($groups as $group) {
                    Cache::forget("settings:group:{$group}");
                }
            } catch (\Exception $e) {
                // Ignore if query fails
            }
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
}
