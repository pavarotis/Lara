<?php

declare(strict_types=1);

namespace App\Domain\Variables\Services;

use App\Domain\Businesses\Models\Business;
use App\Domain\Variables\Models\Variable;
use Illuminate\Support\Facades\Cache;

/**
 * VariableService
 *
 * Service for loading and managing variables from database.
 * Provides caching and type casting for optimal performance.
 */
class VariableService
{
    /**
     * Get all variables for active business (cached)
     */
    public function getAllVariables(?Business $business = null): array
    {
        $business = $business ?? Business::active()->first();

        if (! $business) {
            return [];
        }

        $cacheKey = "variables:all:{$business->id}";

        return Cache::remember($cacheKey, 3600, function () use ($business) {
            $variables = Variable::forBusiness($business->id)->get();

            $result = [];
            foreach ($variables as $variable) {
                $result[$variable->key] = [
                    'value' => $this->castValue($variable),
                    'type' => $variable->type,
                    'category' => $variable->category,
                    'description' => $variable->description,
                ];
            }

            return $result;
        });
    }

    /**
     * Get single variable value (cached)
     */
    public function get(string $key, mixed $default = null, ?Business $business = null): mixed
    {
        $business = $business ?? Business::active()->first();

        if (! $business) {
            return $default;
        }

        $cacheKey = "variable:{$business->id}:{$key}";

        return Cache::remember($cacheKey, 3600, function () use ($business, $key, $default) {
            $variable = Variable::forBusiness($business->id)
                ->where('key', $key)
                ->first();

            if (! $variable) {
                return $default;
            }

            return $this->castValue($variable);
        });
    }

    /**
     * Get variables by category
     */
    public function getByCategory(string $category, ?Business $business = null): array
    {
        $all = $this->getAllVariables($business);

        return array_filter($all, function ($var) use ($category) {
            return ($var['category'] ?? null) === $category;
        });
    }

    /**
     * Cast variable value based on type
     */
    protected function castValue(Variable $variable): mixed
    {
        return match ($variable->type) {
            'number' => is_numeric($variable->value)
                ? (str_contains($variable->value, '.') ? (float) $variable->value : (int) $variable->value)
                : 0,
            'boolean' => filter_var($variable->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($variable->value, true) ?? [],
            default => $variable->value ?? '',
        };
    }

    /**
     * Clear all caches for a business
     */
    public function clearCache(?Business $business = null): void
    {
        $business = $business ?? Business::active()->first();

        if (! $business) {
            return;
        }

        // Clear all variables cache
        Cache::forget("variables:all:{$business->id}");

        // Clear individual variable caches (we need to get all keys first)
        $variables = Variable::forBusiness($business->id)->pluck('key');
        foreach ($variables as $key) {
            Cache::forget("variable:{$business->id}:{$key}");
        }
    }

    /**
     * Clear cache for specific variable
     */
    public function clearVariableCache(string $key, ?Business $business = null): void
    {
        $business = $business ?? Business::active()->first();

        if (! $business) {
            return;
        }

        Cache::forget("variable:{$business->id}:{$key}");
        Cache::forget("variables:all:{$business->id}");
    }

    /**
     * Get site configuration as array
     */
    public function getSiteConfig(?Business $business = null): array
    {
        $variables = $this->getAllVariables($business);

        return [
            'site_name' => $variables['site_name']['value'] ?? 'My Store',
            'items_per_page' => $variables['items_per_page']['value'] ?? 12,
            'contact_email' => $variables['contact_email']['value'] ?? '',
            'currency' => $variables['currency']['value'] ?? 'EUR',
            'theme' => $this->getThemeConfig($variables),
            'seo' => $this->getSeoConfig($variables),
            'social' => $this->getSocialConfig($variables),
        ];
    }

    /**
     * Get theme configuration from variables
     */
    protected function getThemeConfig(array $variables): array
    {
        $primaryColor = $variables['primary_color']['value'] ?? '#3b82f6';
        $themeColors = $variables['theme_colors']['value'] ?? [];

        // If theme_colors is JSON, use it, otherwise build from individual colors
        if (is_array($themeColors) && ! empty($themeColors)) {
            return $themeColors;
        }

        return [
            'primary' => $primaryColor,
            'secondary' => $variables['secondary_color']['value'] ?? '#8b5cf6',
            'accent' => $variables['accent_color']['value'] ?? '#10b981',
        ];
    }

    /**
     * Get SEO configuration
     */
    protected function getSeoConfig(array $variables): array
    {
        return [
            'meta_description' => $variables['meta_description']['value'] ?? '',
            'google_analytics_id' => $variables['google_analytics_id']['value'] ?? '',
            'keywords' => $variables['seo_keywords']['value'] ?? [],
        ];
    }

    /**
     * Get social media configuration
     */
    protected function getSocialConfig(array $variables): array
    {
        $socialLinks = $variables['social_links']['value'] ?? [];

        if (is_array($socialLinks) && ! empty($socialLinks)) {
            return $socialLinks;
        }

        return [
            'facebook' => $variables['facebook_url']['value'] ?? '',
            'twitter' => $variables['twitter_url']['value'] ?? '',
        ];
    }
}
