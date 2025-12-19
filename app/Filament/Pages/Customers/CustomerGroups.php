<?php

namespace App\Filament\Pages\Customers;

use Filament\Pages\Page;

class CustomerGroups extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Customers';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.customers.customer-groups';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Customer Groups';

    public function getTitle(): string
    {
        return 'Customer Groups';
    }
}
