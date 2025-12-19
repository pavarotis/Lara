<?php

namespace App\Filament\Pages\System\Maintenance;

use Filament\Pages\Page;

class Uploads extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 18;

    protected string $view = 'filament.pages.system.maintenance.uploads';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static ?string $navigationLabel = 'Uploads';

    public function getTitle(): string
    {
        return 'Uploads';
    }
}
