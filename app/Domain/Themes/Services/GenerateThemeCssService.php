<?php

declare(strict_types=1);

namespace App\Domain\Themes\Services;

use App\Domain\Businesses\Models\Business;

class GenerateThemeCssService
{
    public function __construct(
        private GetThemeTokensService $getThemeTokensService
    ) {}

    /**
     * Generate CSS variables from theme tokens
     */
    public function generate(Business $business): string
    {
        $tokens = $this->getThemeTokensService->getTokens($business);

        $css = ":root {\n";

        // Colors
        if (isset($tokens['colors'])) {
            foreach ($tokens['colors'] as $key => $value) {
                // Skip if value is an array (should be string)
                if (is_array($value)) {
                    // Try to get DEFAULT or first value
                    $value = $value['DEFAULT'] ?? $value[0] ?? '';
                }
                if (is_string($value) && ! empty($value)) {
                    $css .= "    --color-{$key}: {$value};\n";
                }
            }
        }

        // Fonts
        if (isset($tokens['fonts'])) {
            foreach ($tokens['fonts'] as $key => $font) {
                if (is_array($font)) {
                    if (isset($font['family']) && is_string($font['family'])) {
                        $css .= "    --font-{$key}-family: {$font['family']};\n";
                    }
                    if (isset($font['weight']) && is_string($font['weight'])) {
                        $css .= "    --font-{$key}-weight: {$font['weight']};\n";
                    }
                } elseif (is_string($font)) {
                    // If font is a string, use it as family
                    $css .= "    --font-{$key}-family: {$font};\n";
                }
            }
        }

        // Spacing
        if (isset($tokens['spacing'])) {
            foreach ($tokens['spacing'] as $key => $value) {
                // Skip if value is an array
                if (is_array($value)) {
                    $value = $value['DEFAULT'] ?? $value[0] ?? '';
                }
                if (is_string($value) && ! empty($value)) {
                    $css .= "    --spacing-{$key}: {$value};\n";
                }
            }
        }

        // Radius
        if (isset($tokens['radius'])) {
            foreach ($tokens['radius'] as $key => $value) {
                // Skip if value is an array
                if (is_array($value)) {
                    $value = $value['DEFAULT'] ?? $value[0] ?? '';
                }
                if (is_string($value) && ! empty($value)) {
                    $css .= "    --radius-{$key}: {$value};\n";
                }
            }
        }

        $css .= "}\n";

        return $css;
    }
}
