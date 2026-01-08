<?php

declare(strict_types=1);

namespace App\Domain\Themes\Services;

use App\Domain\Businesses\Models\Business;

class ApplyThemeTokensService
{
    public function __construct(
        private GenerateThemeCssService $generateThemeCssService,
        private GetThemeTokensService $getThemeTokensService
    ) {}

    /**
     * Apply theme tokens to views
     */
    public function apply(Business $business): void
    {
        // 1. Generate CSS
        $css = $this->generateThemeCssService->generate($business);

        // 2. Share with views
        view()->share('themeCss', $css);
        view()->share('themeTokens', $this->getThemeTokensService->getTokens($business));
    }
}
