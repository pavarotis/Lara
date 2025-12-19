<?php

namespace App\Filament\Pages\Reports;

use Filament\Pages\Page;

class WhosOnline extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.reports.whos-online';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Who\'s Online';

    public function getTitle(): string
    {
        return 'Who\'s Online';
    }
}
