<?php

namespace App\Filament\Pages\Catalog;

use Filament\Pages\Page;

class Downloads extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 9;

    protected string $view = 'filament.pages.catalog.downloads';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static ?string $navigationLabel = 'Downloads';

    public function getTitle(): string
    {
        return 'Downloads';
    }
}
