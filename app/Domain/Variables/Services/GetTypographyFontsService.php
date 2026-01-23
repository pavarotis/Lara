<?php

declare(strict_types=1);

namespace App\Domain\Variables\Services;

use App\Domain\Businesses\Models\Business;
use App\Support\VariableHelper;
use Illuminate\Support\Facades\Cache;

class GetTypographyFontsService
{
    /**
     * Get typography fonts for a business based on locale
     * Returns array with font families and Google Fonts link
     */
    public function getFonts(Business $business, string $locale = 'en'): array
    {
        // Map locale to typography prefix (en -> en, el -> gr)
        $langPrefix = $this->mapLocaleToLang($locale);

        // Cache key based on business and locale
        $cacheKey = "typography_fonts:{$business->id}:{$langPrefix}";

        return Cache::remember($cacheKey, 3600, function () use ($business, $langPrefix) {
            $fonts = [
                'primary' => VariableHelper::get("typography.{$langPrefix}.font.primary", $business, 'Inter'),
                'secondary' => VariableHelper::get("typography.{$langPrefix}.font.secondary", $business, 'Inter'),
                'heading' => VariableHelper::get("typography.{$langPrefix}.font.heading", $business, 'Inter'),
                'body' => VariableHelper::get("typography.{$langPrefix}.font.body", $business, 'Inter'),
            ];

            // Generate Google Fonts link
            $googleFontsLink = $this->generateGoogleFontsLink($fonts);

            return [
                'fonts' => $fonts,
                'google_fonts_link' => $googleFontsLink,
            ];
        });
    }

    /**
     * Clear typography fonts cache for a business
     */
    public function clearCache(Business $business): void
    {
        Cache::forget("typography_fonts:{$business->id}:en");
        Cache::forget("typography_fonts:{$business->id}:gr");
    }

    /**
     * Map locale to typography language prefix
     * en -> en, el -> gr, default -> en
     */
    protected function mapLocaleToLang(string $locale): string
    {
        return match (strtolower($locale)) {
            'el', 'el_gr', 'gr' => 'gr',
            default => 'en',
        };
    }

    /**
     * Generate Google Fonts link from font names
     * Handles multiple fonts and creates proper URL
     */
    protected function generateGoogleFontsLink(array $fonts): ?string
    {
        // Collect unique font names
        $uniqueFonts = array_unique(array_filter(array_values($fonts)));

        if (empty($uniqueFonts)) {
            return null;
        }

        // Build Google Fonts URL
        // Format: https://fonts.googleapis.com/css2?family=Font+Name:wght@400;500;600;700&family=Another+Font:wght@400;500&display=swap
        $fontFamilies = [];

        foreach ($uniqueFonts as $fontName) {
            // Skip system fonts or fonts that don't need Google Fonts
            if ($this->isSystemFont($fontName)) {
                continue;
            }

            // Replace spaces with + for URL
            $fontNameEncoded = str_replace(' ', '+', $fontName);

            // Default weights (can be extended later)
            $weights = '400;500;600;700';

            $fontFamilies[] = "family={$fontNameEncoded}:wght@{$weights}";
        }

        if (empty($fontFamilies)) {
            return null;
        }

        $queryString = implode('&', $fontFamilies);

        return "https://fonts.googleapis.com/css2?{$queryString}&display=swap";
    }

    /**
     * Check if font is a system font (doesn't need Google Fonts)
     */
    protected function isSystemFont(string $fontName): bool
    {
        $systemFonts = [
            'arial',
            'helvetica',
            'times new roman',
            'times',
            'courier new',
            'courier',
            'verdana',
            'georgia',
            'palatino',
            'garamond',
            'bookman',
            'comic sans ms',
            'trebuchet ms',
            'arial black',
            'impact',
        ];

        return in_array(strtolower($fontName), $systemFonts);
    }
}
