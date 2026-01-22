<?php

declare(strict_types=1);

namespace App\Filament\Pages\CMS\Performance;

use App\Domain\Performance\Models\LighthouseAudit;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Schema;

class Performance extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected string $view = 'filament.pages.cms.performance.performance';

    protected static ?string $navigationLabel = 'Performance';

    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 4;

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
            'lighthouse' => $this->getLighthouseStats(),
            'bundle' => $this->getBundleReport(),
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

    private function getLighthouseStats(): array
    {
        if (! Schema::hasTable('lighthouse_audits')) {
            return [
                'latest' => null,
                'recent' => collect(),
                'average' => null,
            ];
        }

        $recent = LighthouseAudit::orderByDesc('audited_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $latest = $recent->first();

        $average = null;
        if ($recent->count() > 0) {
            $average = [
                'performance' => round((float) $recent->avg('performance'), 1),
                'accessibility' => round((float) $recent->avg('accessibility'), 1),
                'best_practices' => round((float) $recent->avg('best_practices'), 1),
                'seo' => round((float) $recent->avg('seo'), 1),
            ];
        }

        return [
            'latest' => $latest,
            'recent' => $recent,
            'average' => $average,
        ];
    }

    private function getBundleReport(): array
    {
        $path = storage_path('app/performance/bundle-report.json');
        if (! is_file($path)) {
            return [
                'exists' => false,
                'generated_at' => null,
                'totals' => null,
                'files' => [],
            ];
        }

        $raw = file_get_contents($path);
        $report = $raw ? json_decode($raw, true) : null;

        if (! is_array($report)) {
            return [
                'exists' => false,
                'generated_at' => null,
                'totals' => null,
                'files' => [],
            ];
        }

        return [
            'exists' => true,
            'generated_at' => $report['generated_at'] ?? null,
            'totals' => $report['totals'] ?? null,
            'files' => $report['files'] ?? [],
        ];
    }
}
