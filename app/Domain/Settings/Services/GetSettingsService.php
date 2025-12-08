<?php

declare(strict_types=1);

namespace App\Domain\Settings\Services;

use App\Domain\Settings\Models\Setting;
use Illuminate\Support\Facades\Cache;

class GetSettingsService
{
    /**
     * Get all settings (cached)
     */
    public function all(): array
    {
        return Cache::tags(['settings'])->remember('settings:all', now()->addHour(), function () {
            return Setting::all()
                ->mapWithKeys(fn ($setting) => [$setting->key => $this->castValue($setting)])
                ->toArray();
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
        return Cache::tags(['settings'])->remember("settings:group:{$group}", now()->addHour(), function () use ($group) {
            return Setting::group($group)
                ->get()
                ->mapWithKeys(fn ($setting) => [$setting->key => $this->castValue($setting)])
                ->toArray();
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
        Cache::tags(['settings'])->flush();
    }
}
