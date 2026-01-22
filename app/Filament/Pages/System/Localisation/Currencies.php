<?php

namespace App\Filament\Pages\System\Localisation;

use Filament\Pages\Page;

class Currencies extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.system.localisation.currencies';

    public function getTitle(): string
    {
        return 'Currencies';
    }
}
