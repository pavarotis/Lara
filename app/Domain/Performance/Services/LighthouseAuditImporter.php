<?php

declare(strict_types=1);

namespace App\Domain\Performance\Services;

use App\Domain\Performance\Models\LighthouseAudit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class LighthouseAuditImporter
{
    public function importFromPath(string $path, ?string $urlOverride = null, ?string $deviceOverride = null): LighthouseAudit
    {
        if (! is_file($path)) {
            throw new RuntimeException("Lighthouse report not found at path: {$path}");
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new RuntimeException("Unable to read Lighthouse report at path: {$path}");
        }

        $report = json_decode($raw, true);
        if (! is_array($report)) {
            throw new RuntimeException('Invalid Lighthouse JSON report.');
        }

        $categories = $report['categories'] ?? [];

        $url = $urlOverride
            ?? $report['finalUrl']
            ?? $report['requestedUrl']
            ?? 'unknown';

        $device = $deviceOverride
            ?? $report['configSettings']['formFactor']
            ?? 'desktop';

        $auditedAt = null;
        if (! empty($report['fetchTime'])) {
            $auditedAt = Carbon::parse($report['fetchTime']);
        }

        $audit = LighthouseAudit::create([
            'url' => $url,
            'device' => $device,
            'performance' => $this->score($categories, 'performance'),
            'accessibility' => $this->score($categories, 'accessibility'),
            'best_practices' => $this->score($categories, 'best-practices'),
            'seo' => $this->score($categories, 'seo'),
            'pwa' => $this->score($categories, 'pwa'),
            'report_path' => $this->storeReport($raw, $path),
            'audited_at' => $auditedAt,
        ]);

        return $audit;
    }

    private function score(array $categories, string $key): ?float
    {
        $score = $categories[$key]['score'] ?? null;
        if ($score === null) {
            return null;
        }

        return round($score * 100, 1);
    }

    private function storeReport(string $raw, string $sourcePath): string
    {
        $extension = pathinfo($sourcePath, PATHINFO_EXTENSION) ?: 'json';
        $fileName = 'lighthouse_'.now()->format('Ymd_His').'_'.
            Str::random(6).'.'.$extension;
        $storagePath = 'performance/lighthouse/'.$fileName;

        Storage::disk('local')->put($storagePath, $raw);

        return $storagePath;
    }
}
