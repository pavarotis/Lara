<?php

namespace App\Filament\Pages\System\Localisation;

use Filament\Pages\Page;

class Countries extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 11;

    protected string $view = 'filament.pages.system.localisation.countries';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationLabel = 'Countries';

    public function getTitle(): string
    {
        return 'Countries';
    }
}
