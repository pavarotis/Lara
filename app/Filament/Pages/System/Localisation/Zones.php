<?php

namespace App\Filament\Pages\System\Localisation;

use Filament\Pages\Page;

class Zones extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 12;

    protected string $view = 'filament.pages.system.localisation.zones';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'Zones';

    public function getTitle(): string
    {
        return 'Zones';
    }
}
