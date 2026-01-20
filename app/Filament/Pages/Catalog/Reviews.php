<?php

namespace App\Filament\Pages\Catalog;

use Filament\Pages\Page;

class Reviews extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Catalog Spare';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.catalog.reviews';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'Reviews';

    public function getTitle(): string
    {
        return 'Reviews';
    }
}
