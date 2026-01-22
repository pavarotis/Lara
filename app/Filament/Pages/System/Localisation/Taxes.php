<?php

namespace App\Filament\Pages\System\Localisation;

use Filament\Pages\Page;

class Taxes extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.system.localisation.taxes';

    public function getTitle(): string
    {
        return 'Taxes';
    }
}
