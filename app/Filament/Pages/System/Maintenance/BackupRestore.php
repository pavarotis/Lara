<?php

namespace App\Filament\Pages\System\Maintenance;

use Filament\Pages\Page;

class BackupRestore extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 17;

    protected string $view = 'filament.pages.system.maintenance.backup-restore';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationLabel = 'Backup / Restore';

    public function getTitle(): string
    {
        return 'Backup / Restore';
    }
}
