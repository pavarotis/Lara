<?php

declare(strict_types=1);

namespace App\Domain\Modules\Services;

use App\Domain\Modules\Models\ModuleInstance;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

/**
 * RenderModuleService
 *
 * Renders a module instance with 3-level row pattern.
 * 3-level pattern: row → container → content
 */
class RenderModuleService
{
    public function __construct(
        private GetModuleViewService $getModuleViewService
    ) {}

    /**
     * Render module instance
     *
     * Steps:
     * 1. Get module view path (with theme resolution)
     * 2. Get module settings
     * 3. Render module content (inner content)
     * 4. Wrap with module-row component (3-level pattern)
     */
    public function render(ModuleInstance $module): string
    {
        // Skip if module is disabled
        if (! $module->enabled) {
            return '';
        }

        // Cache key with module ID and updated timestamp
        $cacheKey = "module:{$module->id}:{$module->updated_at->timestamp}";
        $ttl = $this->getCacheTtl($module);

        // Try to get from cache
        return Cache::remember($cacheKey, $ttl, function () use ($module) {
            return $this->renderModule($module);
        });
    }

    /**
     * Render module (without cache)
     */
    private function renderModule(ModuleInstance $module): string
    {
        try {
            // 1. Get module view path
            $viewPath = $this->getModuleViewService->getViewPath($module->type, $module->business);

            // 2. Get module settings
            $settings = $module->settings ?? [];

            // 3. Render module content (inner content)
            // Eager load business relationship if not already loaded
            if (! $module->relationLoaded('business')) {
                $module->load('business');
            }

            $moduleContent = View::make($viewPath, [
                'module' => $module,
                'settings' => $settings,
            ])->render();

            // 4. Wrap with module-row component (3-level pattern)
            return View::make('components.module-row', [
                'module' => $module,
                'widthMode' => $module->width_mode ?? 'contained',
                'style' => $module->style ?? [],
            ])->with('slot', $moduleContent)->render();
        } catch (\Exception $e) {
            // Log error but don't break rendering
            Log::warning("Failed to render module {$module->type} (ID: {$module->id}): ".$e->getMessage());

            return "<!-- Error rendering module '{$module->type}' -->";
        }
    }

    /**
     * Get cache TTL for module
     */
    private function getCacheTtl(ModuleInstance $module): int
    {
        $moduleConfig = config("modules.{$module->type}");

        if ($moduleConfig && isset($moduleConfig['cache_ttl'])) {
            return (int) $moduleConfig['cache_ttl'];
        }

        return 3600; // Default 1 hour
    }
}
