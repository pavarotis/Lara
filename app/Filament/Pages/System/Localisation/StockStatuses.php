<?php

namespace App\Filament\Pages\System\Localisation;

use Filament\Pages\Page;

class StockStatuses extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.system.localisation.stock-statuses';

    public function getTitle(): string
    {
        return 'Stock Statuses';
    }
}
