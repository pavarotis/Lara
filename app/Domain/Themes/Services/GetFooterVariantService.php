<?php

declare(strict_types=1);

namespace App\Domain\Themes\Services;

use App\Domain\Businesses\Models\Business;
use App\Domain\Themes\Models\ThemeToken;

class GetFooterVariantService
{
    /**
     * Get footer variant configuration for a business
     */
    public function getVariant(Business $business): array
    {
        // 1. Get from theme_tokens
        $themeToken = ThemeToken::where('business_id', $business->id)->first();
        $variantSlug = $themeToken ? $themeToken->footer_variant : 'simple';

        // 2. Get variant config
        $variants = config('footer_variants');
        $variant = $variants[$variantSlug] ?? $variants['simple'];

        return $variant;
    }
}
