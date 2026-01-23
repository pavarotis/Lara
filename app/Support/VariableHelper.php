<?php

declare(strict_types=1);

namespace App\Support;

use App\Domain\Variables\Services\VariableService;
use Illuminate\Support\Facades\App;

/**
 * VariableHelper
 *
 * Global helper functions for accessing variables easily.
 * Usage: variable('site_name') or variable('theme_colors', [])
 */
if (! function_exists('variable')) {
    function variable(string $key, mixed $default = null): mixed
    {
        $service = App::make(VariableService::class);

        return $service->get($key, $default);
    }
}

if (! function_exists('site_config')) {
    function site_config(): array
    {
        $service = App::make(VariableService::class);

        return $service->getSiteConfig();
    }
}

if (! function_exists('theme_css')) {
    function theme_css(): string
    {
        $themeService = App::make(\App\Domain\Variables\Services\ThemeService::class);

        return $themeService->getCssStyleTag();
    }
}
