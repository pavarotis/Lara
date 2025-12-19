<?php

namespace App\Filament\Pages\Catalog;

use Filament\Pages\Page;

class Manufacturers extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 8;

    protected string $view = 'filament.pages.catalog.manufacturers';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'Manufacturers';

    public function getTitle(): string
    {
        return 'Manufacturers';
    }
}
