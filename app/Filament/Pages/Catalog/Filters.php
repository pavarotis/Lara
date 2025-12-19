<?php

namespace App\Filament\Pages\Catalog;

use Filament\Pages\Page;

class Filters extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.catalog.filters';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-funnel';

    protected static ?string $navigationLabel = 'Filters';

    public function getTitle(): string
    {
        return 'Filters';
    }
}
