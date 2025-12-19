<?php

namespace App\Filament\Pages\Catalog;

use Filament\Pages\Page;

class Products extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.catalog.products';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Products';

    /**
     * Use a custom slug to avoid clashing with the legacy /admin/products routes.
     * This will register the Filament page at /admin/catalog-products with route name
     * filament.admin.pages.catalog-products.
     */
    public static function getSlug(?\Filament\Panel $panel = null): string
    {
        return 'catalog-products';
    }

    public function getTitle(): string
    {
        return 'Products';
    }
}
