<?php

namespace App\Filament\Pages\System\Localisation;

use Filament\Pages\Page;

class Returns extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.system.localisation.returns';

    public function getTitle(): string
    {
        return 'Returns';
    }
}
