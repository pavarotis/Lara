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
                $css .= "    --color-{$key}: {$value};\n";
            }
        }

        // Fonts
        if (isset($tokens['fonts'])) {
            foreach ($tokens['fonts'] as $key => $font) {
                if (is_array($font)) {
                    $css .= "    --font-{$key}-family: {$font['family']};\n";
                    $css .= "    --font-{$key}-weight: {$font['weight']};\n";
                }
            }
        }

        // Spacing
        if (isset($tokens['spacing'])) {
            foreach ($tokens['spacing'] as $key => $value) {
                $css .= "    --spacing-{$key}: {$value};\n";
            }
        }

        // Radius
        if (isset($tokens['radius'])) {
            foreach ($tokens['radius'] as $key => $value) {
                $css .= "    --radius-{$key}: {$value};\n";
            }
        }

        $css .= "}\n";

        return $css;
    }
}
