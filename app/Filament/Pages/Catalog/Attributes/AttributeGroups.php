<?php

namespace App\Filament\Pages\Catalog\Attributes;

use Filament\Pages\Page;

class AttributeGroups extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.pages.catalog.attributes.attribute-groups';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Attribute Groups';

    public function getTitle(): string
    {
        return 'Attribute Groups';
    }
}
