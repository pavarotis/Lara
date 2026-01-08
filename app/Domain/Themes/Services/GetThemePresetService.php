<?php

declare(strict_types=1);

namespace App\Domain\Themes\Services;

use App\Domain\Businesses\Models\Business;
use App\Domain\Themes\Models\ThemePreset;
use App\Domain\Themes\Models\ThemeToken;

class GetThemePresetService
{
    /**
     * Get theme preset for a business
     */
    public function getPreset(Business $business): ?ThemePreset
    {
        $themeToken = ThemeToken::where('business_id', $business->id)->first();

        $presetSlug = $themeToken ? $themeToken->preset_slug : 'cafe';

        return ThemePreset::where('slug', $presetSlug)->where('is_active', true)->first();
    }
}
