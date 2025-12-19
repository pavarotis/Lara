<?php

namespace App\Filament\Pages\Reports;

use Filament\Pages\Page;

class Reports extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.reports.reports';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Reports';

    public function getTitle(): string
    {
        return 'Reports';
    }
}
