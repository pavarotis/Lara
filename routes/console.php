<?php

use App\Domain\Performance\Services\LighthouseAuditImporter;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('performance:lighthouse:import {path : Path to Lighthouse JSON report} {--url=} {--device=}', function () {
    $path = (string) $this->argument('path');
    $url = $this->option('url') ?: null;
    $device = $this->option('device') ?: null;

    $audit = app(LighthouseAuditImporter::class)->importFromPath($path, $url, $device);

    $this->info('Lighthouse audit imported.');
    $this->line("URL: {$audit->url}");
    $this->line("Device: {$audit->device}");
    $this->line("Scores: perf {$audit->performance} / bp {$audit->best_practices} / seo {$audit->seo}");
})->purpose('Import a Lighthouse JSON report into the performance dashboard');
