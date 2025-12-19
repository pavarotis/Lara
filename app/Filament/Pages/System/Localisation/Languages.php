<?php

namespace App\Filament\Pages\System\Localisation;

use Filament\Pages\Page;

class Languages extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.pages.system.localisation.languages';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-language';

    protected static ?string $navigationLabel = 'Languages';

    public function getTitle(): string
    {
        return 'Languages';
    }
}
