<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Domain\Variables\Services\ThemeService;
use Illuminate\View\Component;
use Illuminate\View\View;

class DynamicTheme extends Component
{
    protected ThemeService $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
    }

    public function render(): View
    {
        return view('components.dynamic-theme', [
            'cssTag' => $this->themeService->getCssStyleTag(),
        ]);
    }
}
