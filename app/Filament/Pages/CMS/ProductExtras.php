<?php

namespace App\Filament\Pages\CMS;

use Filament\Pages\Page;

class ProductExtras extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 9;

    protected string $view = 'filament.pages.cms.product-extras';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'Product Extras';

    public function getTitle(): string
    {
        return 'Product Extras';
    }
}
