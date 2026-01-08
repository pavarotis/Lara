<?php

declare(strict_types=1);

namespace App\Filament\Pages\CMS\Performance;

use Filament\Pages\Page;

class Performance extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected string $view = 'filament.pages.cms.performance.performance';

    protected static ?string $navigationLabel = 'Performance';

    protected static ?int $navigationSort = 100;

    public function mount(): void
    {
        //
    }

    public function getPerformanceData(): array
    {
        // Get cache statistics
        $cacheStats = $this->getCacheStats();

        // Get layout compilation stats
        $compilationStats = $this->getCompilationStats();

        // Get asset stats
        $assetStats = $this->getAssetStats();

        return [
            'cache' => $cacheStats,
            'compilation' => $compilationStats,
            'assets' => $assetStats,
        ];
    }

    private function getCacheStats(): array
    {
        // Get cache hit/miss statistics
        // This would require cache tagging and tracking
        return [
            'hit_rate' => 0, // Placeholder
            'miss_rate' => 0, // Placeholder
            'total_requests' => 0, // Placeholder
            'cached_pages' => 0, // Placeholder
        ];
    }

    private function getCompilationStats(): array
    {
        $compiledLayouts = \App\Domain\Layouts\Models\Layout::whereNotNull('compiled_at')->count();
        $totalLayouts = \App\Domain\Layouts\Models\Layout::count();

        return [
            'compiled' => $compiledLayouts,
            'total' => $totalLayouts,
            'percentage' => $totalLayouts > 0 ? round(($compiledLayouts / $totalLayouts) * 100, 2) : 0,
        ];
    }

    private function getAssetStats(): array
    {
        // Get widget asset statistics
        $modules = config('modules', []);
        $modulesWithAssets = 0;
        $totalAssets = 0;

        foreach ($modules as $moduleConfig) {
            $assets = $moduleConfig['assets'] ?? [];
            if (! empty($assets['css']) || ! empty($assets['js'])) {
                $modulesWithAssets++;
                $totalAssets += count($assets['css'] ?? []) + count($assets['js'] ?? []);
            }
        }

        return [
            'modules_with_assets' => $modulesWithAssets,
            'total_modules' => count($modules),
            'total_assets' => $totalAssets,
        ];
    }
}
