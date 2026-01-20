<?php

namespace App\Filament\Pages\Catalog;

use Filament\Pages\Page;

class Options extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Catalog Spare';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.catalog.options';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationLabel = 'Options';

    public function getTitle(): string
    {
        return 'Options';
    }
}
