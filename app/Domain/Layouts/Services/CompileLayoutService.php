<?php

declare(strict_types=1);

namespace App\Domain\Layouts\Services;

use App\Domain\Layouts\Models\Layout;
use App\Domain\Modules\Models\ContentModuleAssignment;
use App\Domain\Modules\Models\ModuleInstance;
use App\Domain\Modules\Services\CollectWidgetAssetsService;
use App\Domain\Modules\Services\RenderModuleService;
use Illuminate\Database\Eloquent\Collection;

class CompileLayoutService
{
    public function __construct(
        private RenderModuleService $renderModuleService,
        private CollectWidgetAssetsService $collectWidgetAssetsService
    ) {}

    /**
     * Compile layout JSON to HTML + assets
     *
     * @return array{compiled_html: string, assets_manifest: array, critical_css: string}
     */
    public function compile(Layout $layout): array
    {
        // 1. Get modules for layout (from all regions)
        $modules = $this->getModulesForLayout($layout);

        // 2. Render modules to HTML
        $compiledHtml = $this->renderModules($modules);

        // 3. Collect assets from modules
        $assetsManifest = $this->collectWidgetAssetsService->collect($modules);

        // 4. Get critical CSS
        $criticalCss = $this->collectWidgetAssetsService->getCriticalCss($modules);

        return [
            'compiled_html' => $compiledHtml,
            'assets_manifest' => $assetsManifest,
            'critical_css' => $criticalCss,
        ];
    }

    /**
     * Get modules for layout
     *
     * @return Collection<ModuleInstance>
     */
    private function getModulesForLayout(Layout $layout): Collection
    {
        $regions = $layout->getRegions();
        $moduleIds = [];

        // Get all module assignments for content using this layout
        $assignments = ContentModuleAssignment::whereHas('content', function ($query) use ($layout) {
            $query->where('layout_id', $layout->id);
        })->get();

        foreach ($assignments as $assignment) {
            if (! in_array($assignment->module_instance_id, $moduleIds, true)) {
                $moduleIds[] = $assignment->module_instance_id;
            }
        }

        // Also get default modules for layout (if any)
        // This would be modules assigned to the layout itself, not content

        return ModuleInstance::whereIn('id', $moduleIds)
            ->where('enabled', true)
            ->where('business_id', $layout->business_id)
            ->get();
    }

    /**
     * Render modules to HTML
     *
     * @param  Collection<ModuleInstance>  $modules
     * @return string Rendered HTML
     */
    private function renderModules(Collection $modules): string
    {
        $html = [];

        foreach ($modules as $module) {
            $html[] = $this->renderModuleService->render($module);
        }

        return implode("\n", $html);
    }
}
