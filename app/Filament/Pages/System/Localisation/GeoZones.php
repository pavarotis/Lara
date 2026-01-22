<?php

namespace App\Filament\Pages\System\Localisation;

use Filament\Pages\Page;

class GeoZones extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.system.localisation.geo-zones';

    public function getTitle(): string
    {
        return 'Geo Zones';
    }
}
