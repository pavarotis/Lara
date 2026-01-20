<?php

declare(strict_types=1);

namespace App\Domain\Modules\Services;

use App\Domain\Businesses\Models\Business;
use Illuminate\Support\Facades\View;

/**
 * GetModuleViewService
 *
 * Resolves module view path with theme fallback.
 */
class GetModuleViewService
{
    /**
     * Get view path for module type with theme resolution
     */
    public function getViewPath(string $moduleType, ?Business $business = null): string
    {
        // 0. Use explicit module view if provided (e.g. plugin namespace)
        $explicitView = config("modules.{$moduleType}.view");
        if ($explicitView && View::exists($explicitView)) {
            return $explicitView;
        }

        // 1. Get theme from business
        $theme = $business ? $business->getTheme() : 'default';

        // 2. Try theme-specific view
        $themeViewPath = "themes.{$theme}.modules.{$moduleType}";
        if (View::exists($themeViewPath)) {
            return $themeViewPath;
        }

        // 3. Fallback to default theme
        $defaultViewPath = "themes.default.modules.{$moduleType}";
        if (View::exists($defaultViewPath)) {
            return $defaultViewPath;
        }

        // 4. Fallback to generic module view
        return "modules.{$moduleType}";
    }
}
