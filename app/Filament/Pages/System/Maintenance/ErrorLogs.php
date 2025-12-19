<?php

namespace App\Filament\Pages\System\Maintenance;

use Filament\Pages\Page;

class ErrorLogs extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 19;

    protected string $view = 'filament.pages.system.maintenance.error-logs';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationLabel = 'Error Logs';

    public function getTitle(): string
    {
        return 'Error Logs';
    }
}
