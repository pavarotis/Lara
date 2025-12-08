<?php

declare(strict_types=1);

namespace App\Domain\Settings\Services;

use App\Domain\Settings\Models\Setting;

class UpdateSettingsService
{
    public function __construct(
        private GetSettingsService $getSettings
    ) {}

    /**
     * Update a setting
     */
    public function execute(string $key, mixed $value, string $type = 'string', string $group = 'general'): Setting
    {
        $setting = Setting::firstOrNew(['key' => $key]);
        $setting->value = $this->prepareValue($value, $type);
        $setting->type = $type;
        $setting->group = $group;
        $setting->save();

        // Clear cache
        $this->getSettings->clearCache();

        return $setting;
    }

    /**
     * Update multiple settings
     */
    public function executeMany(array $settings): void
    {
        foreach ($settings as $key => $data) {
            $this->execute(
                $key,
                $data['value'] ?? null,
                $data['type'] ?? 'string',
                $data['group'] ?? 'general'
            );
        }
    }

    /**
     * Prepare value for storage
     */
    private function prepareValue(mixed $value, string $type): mixed
    {
        return match ($type) {
            'json' => is_array($value) ? json_encode($value) : $value,
            default => $value,
        };
    }
}
