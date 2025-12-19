<?php

namespace App\Filament\Pages\System\Localisation;

use Filament\Pages\Page;

class Returns extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.system.localisation.returns';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $navigationLabel = 'Returns';

    public function getTitle(): string
    {
        return 'Returns';
    }
}
