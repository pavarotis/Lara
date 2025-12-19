<?php

namespace App\Filament\Pages\Sales;

use Filament\Pages\Page;

class RecurringOrders extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.sales.recurring-orders';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationLabel = 'Recurring Orders';

    public function getTitle(): string
    {
        return 'Recurring Orders';
    }
}
