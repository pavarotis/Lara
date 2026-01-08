<?php

declare(strict_types=1);

namespace App\Domain\Themes\Services;

use App\Domain\Businesses\Models\Business;
use App\Domain\Themes\Models\ThemeToken;
use Illuminate\Support\Facades\DB;

class UpdateThemeTokensService
{
    /**
     * Update theme tokens for a business
     */
    public function execute(Business $business, array $data): void
    {
        DB::transaction(function () use ($business, $data) {
            // Update business settings
            $settings = $business->settings ?? [];
            if (isset($data['preset'])) {
                $settings['theme_preset'] = $data['preset'];
            }
            if (isset($data['header_variant'])) {
                $settings['header_variant'] = $data['header_variant'];
            }
            if (isset($data['footer_variant'])) {
                $settings['footer_variant'] = $data['footer_variant'];
            }
            $business->update(['settings' => $settings]);

            // Update or create theme token
            $themeToken = ThemeToken::firstOrNew(['business_id' => $business->id]);
            $themeToken->preset_slug = $data['preset'] ?? $themeToken->preset_slug ?? 'cafe';
            $themeToken->token_overrides = $data['token_overrides'] ?? [];
            if (isset($data['header_variant'])) {
                $themeToken->header_variant = $data['header_variant'];
            }
            if (isset($data['footer_variant'])) {
                $themeToken->footer_variant = $data['footer_variant'];
            }
            $themeToken->save();
        });
    }
}
