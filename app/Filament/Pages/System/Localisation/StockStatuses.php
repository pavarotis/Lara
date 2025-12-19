<?php

namespace App\Filament\Pages\System\Localisation;

use Filament\Pages\Page;

class StockStatuses extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 8;

    protected string $view = 'filament.pages.system.localisation.stock-statuses';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-check-badge';

    protected static ?string $navigationLabel = 'Stock Statuses';

    public function getTitle(): string
    {
        return 'Stock Statuses';
    }
}
