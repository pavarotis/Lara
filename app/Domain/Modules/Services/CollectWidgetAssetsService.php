<?php

declare(strict_types=1);

namespace App\Domain\Modules\Services;

use App\Domain\Modules\Models\ModuleInstance;
use Illuminate\Database\Eloquent\Collection;

class CollectWidgetAssetsService
{
    /**
     * Collect assets from module instances
     *
     * @param  Collection<ModuleInstance>  $modules
     * @return array<string, array<string>> ['css' => [...], 'js' => [...]]
     */
    public function collect(Collection $modules): array
    {
        $css = [];
        $js = [];

        foreach ($modules as $module) {
            $moduleConfig = config("modules.{$module->type}");

            if (! $moduleConfig) {
                continue;
            }

            // Get assets from config
            $assets = $moduleConfig['assets'] ?? [];

            // Collect CSS (deduplicate)
            if (isset($assets['css']) && is_array($assets['css'])) {
                foreach ($assets['css'] as $cssFile) {
                    if (! in_array($cssFile, $css, true)) {
                        $css[] = $cssFile;
                    }
                }
            }

            // Collect JS (deduplicate)
            if (isset($assets['js']) && is_array($assets['js'])) {
                foreach ($assets['js'] as $jsFile) {
                    if (! in_array($jsFile, $js, true)) {
                        $js[] = $jsFile;
                    }
                }
            }
        }

        return [
            'css' => $css,
            'js' => $js,
        ];
    }

    /**
     * Get critical CSS from modules
     *
     * @param  Collection<ModuleInstance>  $modules
     * @return string Aggregated critical CSS
     */
    public function getCriticalCss(Collection $modules): string
    {
        $criticalCss = [];

        foreach ($modules as $module) {
            $moduleConfig = config("modules.{$module->type}");

            if (! $moduleConfig) {
                continue;
            }

            $css = $moduleConfig['critical_css'] ?? null;

            if ($css && ! in_array($css, $criticalCss, true)) {
                $criticalCss[] = $css;
            }
        }

        return implode("\n", $criticalCss);
    }
}
