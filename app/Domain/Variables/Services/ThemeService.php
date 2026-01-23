<?php

declare(strict_types=1);

namespace App\Domain\Variables\Services;

use App\Domain\Businesses\Models\Business;
use Illuminate\Support\Facades\Cache;

/**
 * ThemeService
 *
 * Generates CSS variables from theme colors stored in variables table.
 * Creates dynamic CSS that can be injected into the site.
 */
class ThemeService
{
    protected VariableService $variableService;

    public function __construct(VariableService $variableService)
    {
        $this->variableService = $variableService;
    }

    /**
     * Generate CSS variables from theme colors
     */
    public function generateCssVariables(?Business $business = null): string
    {
        $business = $business ?? Business::active()->first();

        if (! $business) {
            return $this->getDefaultCss();
        }

        $cacheKey = "theme:css:{$business->id}";

        return Cache::remember($cacheKey, 3600, function () use ($business) {
            $config = $this->variableService->getSiteConfig($business);
            $theme = $config['theme'] ?? [];

            $css = ":root {\n";

            // Primary colors
            if (isset($theme['primary'])) {
                $css .= "    --color-primary: {$theme['primary']};\n";
                $css .= '    --color-primary-dark: '.$this->darkenColor($theme['primary']).";\n";
                $css .= '    --color-primary-light: '.$this->lightenColor($theme['primary']).";\n";
            }

            if (isset($theme['secondary'])) {
                $css .= "    --color-secondary: {$theme['secondary']};\n";
            }

            if (isset($theme['accent'])) {
                $css .= "    --color-accent: {$theme['accent']};\n";
            }

            // Additional colors from JSON
            foreach ($theme as $key => $value) {
                if (! in_array($key, ['primary', 'secondary', 'accent']) && is_string($value)) {
                    $css .= "    --color-{$key}: {$value};\n";
                }
            }

            $css .= "}\n";

            return $css;
        });
    }

    /**
     * Get CSS as HTML style tag
     */
    public function getCssStyleTag(?Business $business = null): string
    {
        $css = $this->generateCssVariables($business);

        return "<style id='dynamic-theme-css'>{$css}</style>";
    }

    /**
     * Clear theme cache
     */
    public function clearCache(?Business $business = null): void
    {
        $business = $business ?? Business::active()->first();

        if (! $business) {
            return;
        }

        Cache::forget("theme:css:{$business->id}");
    }

    /**
     * Darken a hex color
     */
    protected function darkenColor(string $hex, float $percent = 0.2): string
    {
        $hex = ltrim($hex, '#');
        $rgb = array_map('hexdec', str_split($hex, 2));

        foreach ($rgb as &$color) {
            $color = max(0, min(255, $color * (1 - $percent)));
        }

        return '#'.implode('', array_map(function ($c) {
            return str_pad(dechex((int) $c), 2, '0', STR_PAD_LEFT);
        }, $rgb));
    }

    /**
     * Lighten a hex color
     */
    protected function lightenColor(string $hex, float $percent = 0.2): string
    {
        $hex = ltrim($hex, '#');
        $rgb = array_map('hexdec', str_split($hex, 2));

        foreach ($rgb as &$color) {
            $color = min(255, $color + (255 - $color) * $percent);
        }

        return '#'.implode('', array_map(function ($c) {
            return str_pad(dechex((int) $c), 2, '0', STR_PAD_LEFT);
        }, $rgb));
    }

    /**
     * Default CSS if no theme found
     */
    protected function getDefaultCss(): string
    {
        return ":root {
    --color-primary: #3b82f6;
    --color-primary-dark: #2563eb;
    --color-primary-light: #60a5fa;
    --color-secondary: #8b5cf6;
    --color-accent: #10b981;
}\n";
    }
}
