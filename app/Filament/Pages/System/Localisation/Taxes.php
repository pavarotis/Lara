<?php

namespace App\Filament\Pages\System\Localisation;

use Filament\Pages\Page;

class Taxes extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 14;

    protected string $view = 'filament.pages.system.localisation.taxes';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationLabel = 'Taxes';

    public function getTitle(): string
    {
        return 'Taxes';
    }
}
