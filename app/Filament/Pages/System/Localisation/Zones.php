<?php

namespace App\Filament\Pages\System\Localisation;

use Filament\Pages\Page;

class Zones extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.system.localisation.zones';

    public function getTitle(): string
    {
        return 'Zones';
    }
}
