<?php

namespace App\Filament\Pages\Sales;

use Filament\Pages\Page;

class Orders extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.sales.orders';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Orders';

    /**
     * Use a custom slug to avoid clashing with the legacy /admin/orders routes.
     * This will register the Filament page at /admin/sales-orders with route name
     * filament.admin.pages.sales-orders.
     */
    public static function getSlug(?\Filament\Panel $panel = null): string
    {
        return 'sales-orders';
    }

    public function getTitle(): string
    {
        return 'Orders';
    }
}
