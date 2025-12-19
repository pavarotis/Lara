<?php

namespace App\Filament\Pages\Customers;

use Filament\Pages\Page;

class Customers extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Customers';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.customers.customers';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Customers';

    public function getTitle(): string
    {
        return 'Customers';
    }
}
