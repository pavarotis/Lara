<?php

namespace App\Filament\Pages\CMS;

use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.cms.dashboard';

    public function getTitle(): string
    {
        return 'CMS Dashboard';
    }
}
