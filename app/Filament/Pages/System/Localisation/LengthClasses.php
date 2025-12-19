<?php

namespace App\Filament\Pages\System\Localisation;

use Filament\Pages\Page;

class LengthClasses extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 15;

    protected string $view = 'filament.pages.system.localisation.length-classes';

    // Use a standard icon that exists in the Heroicons set to avoid "o-ruler" missing SVG.
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-scale';

    protected static ?string $navigationLabel = 'Length Classes';

    public function getTitle(): string
    {
        return 'Length Classes';
    }
}
