<?php

namespace App\Filament\Pages\Catalog;

use Filament\Pages\Page;

class Information extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Catalog Spare';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.catalog.information';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-information-circle';

    protected static ?string $navigationLabel = 'Information';

    public function getTitle(): string
    {
        return 'Information';
    }
}
