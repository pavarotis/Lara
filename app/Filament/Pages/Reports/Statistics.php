<?php

namespace App\Filament\Pages\Reports;

use Filament\Pages\Page;

class Statistics extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.reports.statistics';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?string $navigationLabel = 'Statistics';

    public function getTitle(): string
    {
        return 'Statistics';
    }
}
