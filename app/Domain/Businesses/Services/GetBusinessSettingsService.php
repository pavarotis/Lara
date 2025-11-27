<?php

declare(strict_types=1);

namespace App\Domain\Businesses\Services;

use App\Domain\Businesses\Models\Business;

class GetBusinessSettingsService
{
    /**
     * Get all settings for a business with defaults
     */
    public function execute(Business $business): array
    {
        $defaults = $this->getDefaults($business->type);
        $settings = $business->settings ?? [];

        return array_merge($defaults, $settings);
    }

    /**
     * Get a specific setting
     */
    public function get(Business $business, string $key, mixed $default = null): mixed
    {
        $settings = $this->execute($business);
        return data_get($settings, $key, $default);
    }

    /**
     * Get default settings based on business type
     */
    public function getDefaults(string $type): array
    {
        $common = [
            'delivery_enabled' => false,
            'pickup_enabled' => true,
            'show_catalog_images' => true,
            'color_theme' => 'default',
            'currency' => 'EUR',
            'currency_symbol' => '€',
            'minimum_order' => 0,
            'tax_rate' => 0.24,
            'operating_hours' => null,
        ];

        $typeDefaults = match ($type) {
            'cafe' => [
                'color_theme' => 'warm',
                'delivery_enabled' => true,
            ],
            'restaurant' => [
                'color_theme' => 'elegant',
                'delivery_enabled' => true,
                'minimum_order' => 10,
            ],
            'bakery' => [
                'color_theme' => 'warm',
                'delivery_enabled' => false,
            ],
            'gas_station' => [
                'color_theme' => 'industrial',
                'delivery_enabled' => false,
                'show_catalog_images' => false,
            ],
            'salon' => [
                'color_theme' => 'modern',
                'delivery_enabled' => false,
            ],
            default => [],
        };

        return array_merge($common, $typeDefaults);
    }

    /**
     * Get available themes
     */
    public function getAvailableThemes(): array
    {
        return [
            'default' => [
                'name' => 'Default',
                'primary' => '#D97706',
                'accent' => '#0D9488',
            ],
            'warm' => [
                'name' => 'Warm',
                'primary' => '#DC2626',
                'accent' => '#F59E0B',
            ],
            'elegant' => [
                'name' => 'Elegant',
                'primary' => '#1F2937',
                'accent' => '#D4AF37',
            ],
            'modern' => [
                'name' => 'Modern',
                'primary' => '#6366F1',
                'accent' => '#EC4899',
            ],
            'industrial' => [
                'name' => 'Industrial',
                'primary' => '#374151',
                'accent' => '#EF4444',
            ],
        ];
    }

    /**
     * Get theme colors for a business
     */
    public function getThemeColors(Business $business): array
    {
        $themeName = $this->get($business, 'color_theme', 'default');
        $themes = $this->getAvailableThemes();

        return $themes[$themeName] ?? $themes['default'];
    }
}

