<?php

namespace App\Filament\Pages\System\Localisation;

use Filament\Pages\Page;

class StoreLocation extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.system.localisation.store-location';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationLabel = 'Store Location';

    public function getTitle(): string
    {
        return 'Store Location';
    }
}
