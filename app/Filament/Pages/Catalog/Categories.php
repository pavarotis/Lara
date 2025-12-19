<?php

namespace App\Filament\Pages\Catalog;

use Filament\Pages\Page;

class Categories extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.catalog.categories';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationLabel = 'Categories';

    public function getTitle(): string
    {
        return 'Categories';
    }
}
