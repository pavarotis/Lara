<?php

declare(strict_types=1);

namespace App\Domain\Layouts\Services;

use App\Domain\Content\Models\Content;
use App\Domain\Layouts\Models\Layout;
use App\Domain\Modules\Services\GetModulesForRegionService;
use App\Domain\Modules\Services\RenderModuleService;
use Illuminate\Support\Facades\View;

/**
 * RenderLayoutService
 *
 * Renders layout with regions & modules (OpenCart-like system).
 */
class RenderLayoutService
{
    public function __construct(
        private GetLayoutService $getLayoutService,
        private GetModulesForRegionService $getModulesService,
        private RenderModuleService $renderModuleService
    ) {}

    /**
     * Render layout for content
     */
    public function render(Content $content): string
    {
        // 1. Get layout (from content or default)
        $layout = $this->getLayout($content);

        if (! $layout) {
            return '<!-- No layout found -->';
        }

        // 2. Get regions
        $regions = $layout->getRegions();

        // 3. Render each region
        $renderedRegions = [];
        foreach ($regions as $region) {
            $modules = $this->getModulesService->forContentRegion($content, $region);
            $renderedRegions[$region] = $this->renderRegion($modules, $region);
        }

        // 4. Build layout HTML
        return $this->buildLayoutHtml($layout, $renderedRegions);
    }

    /**
     * Get layout for content (from content or default)
     */
    private function getLayout(Content $content): ?Layout
    {
        // Ensure content has business loaded
        if (! $content->relationLoaded('business')) {
            $content->load('business');
        }

        if ($content->layout_id) {
            return $this->getLayoutService->withRegions($content->layout_id);
        }

        return $this->getLayoutService->defaultForBusiness($content->business_id);
    }

    /**
     * Render region with modules
     */
    private function renderRegion($modules, string $region): string
    {
        $html = [];
        foreach ($modules as $module) {
            $html[] = $this->renderModuleService->render($module);
        }

        return implode("\n", $html);
    }

    /**
     * Build layout HTML
     */
    private function buildLayoutHtml(Layout $layout, array $renderedRegions): string
    {
        // Get layout view path
        $layoutViewPath = $this->getLayoutViewPath($layout);

        // Render layout view with regions
        return View::make($layoutViewPath, [
            'layout' => $layout,
            'regions' => $renderedRegions,
        ])->render();
    }

    /**
     * Get layout view path
     */
    private function getLayoutViewPath(Layout $layout): string
    {
        // Try theme-specific layout view
        $theme = $layout->business->getTheme();
        $themeViewPath = "themes.{$theme}.layouts.{$layout->type}";
        if (View::exists($themeViewPath)) {
            return $themeViewPath;
        }

        // Fallback to default theme
        $defaultViewPath = "themes.default.layouts.{$layout->type}";
        if (View::exists($defaultViewPath)) {
            return $defaultViewPath;
        }

        // Fallback to generic layout view
        return "layouts.{$layout->type}";
    }
}
