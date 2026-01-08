<?php

declare(strict_types=1);

namespace App\Domain\Themes\Services;

use App\Domain\Businesses\Models\Business;
use App\Domain\Themes\Models\ThemePreset;
use App\Domain\Themes\Models\ThemeToken;

class GetThemeTokensService
{
    /**
     * Get theme tokens for a business (preset + overrides)
     */
    public function getTokens(Business $business): array
    {
        // 1. Get business theme token (if exists)
        $themeToken = ThemeToken::where('business_id', $business->id)->first();

        // 2. Get preset
        $presetSlug = $themeToken ? $themeToken->preset_slug : 'cafe'; // Default to 'cafe'
        $preset = ThemePreset::where('slug', $presetSlug)->where('is_active', true)->first();

        // 3. If no preset found, use default tokens
        if (! $preset) {
            return $this->getDefaultTokens();
        }

        // 4. Merge: preset tokens + business overrides
        $tokens = $preset->tokens;
        if ($themeToken && $themeToken->token_overrides) {
            $tokens = array_merge_recursive($tokens, $themeToken->token_overrides);
        }

        return $tokens;
    }

    /**
     * Get default tokens if no preset is found
     */
    private function getDefaultTokens(): array
    {
        return [
            'colors' => [
                'primary' => '#D97706',
                'secondary' => '#F59E0B',
                'background' => '#FFFFFF',
                'text' => '#1F2937',
                'accent' => '#FCD34D',
            ],
            'fonts' => [
                'heading' => ['family' => 'Outfit', 'weight' => '700'],
                'body' => ['family' => 'Outfit', 'weight' => '400'],
            ],
            'spacing' => ['section' => '4rem', 'gap' => '2rem'],
            'radius' => ['small' => '0.5rem', 'medium' => '1rem', 'large' => '1.5rem'],
        ];
    }
}
