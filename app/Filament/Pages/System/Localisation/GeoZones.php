<?php

namespace App\Filament\Pages\System\Localisation;

use Filament\Pages\Page;

class GeoZones extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 13;

    protected string $view = 'filament.pages.system.localisation.geo-zones';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-americas';

    protected static ?string $navigationLabel = 'Geo Zones';

    public function getTitle(): string
    {
        return 'Geo Zones';
    }
}
