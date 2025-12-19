<?php

namespace App\Filament\Pages\System\Localisation;

use Filament\Pages\Page;

class Currencies extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 7;

    protected string $view = 'filament.pages.system.localisation.currencies';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Currencies';

    public function getTitle(): string
    {
        return 'Currencies';
    }
}
