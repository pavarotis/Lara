<?php

namespace App\Filament\Pages\Catalog\Attributes;

use Filament\Pages\Page;

class Attributes extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.catalog.attributes.attributes';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Attributes';

    public function getTitle(): string
    {
        return 'Attributes';
    }
}
