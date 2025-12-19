<?php

namespace App\Filament\Pages\System\Localisation;

use Filament\Pages\Page;

class WeightClasses extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 16;

    protected string $view = 'filament.pages.system.localisation.weight-classes';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-scale';

    protected static ?string $navigationLabel = 'Weight Classes';

    public function getTitle(): string
    {
        return 'Weight Classes';
    }
}
