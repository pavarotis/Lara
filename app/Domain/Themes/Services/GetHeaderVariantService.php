<?php

declare(strict_types=1);

namespace App\Domain\Themes\Services;

use App\Domain\Businesses\Models\Business;
use App\Domain\Themes\Models\ThemeToken;

class GetHeaderVariantService
{
    /**
     * Get header variant configuration for a business
     */
    public function getVariant(Business $business): array
    {
        // 1. Get from theme_tokens
        $themeToken = ThemeToken::where('business_id', $business->id)->first();
        $variantSlug = $themeToken ? $themeToken->header_variant : 'minimal';

        // 2. Get variant config
        $variants = config('header_variants');
        $variant = $variants[$variantSlug] ?? $variants['minimal'];

        return $variant;
    }
}
