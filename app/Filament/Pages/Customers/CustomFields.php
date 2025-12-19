<?php

namespace App\Filament\Pages\Customers;

use Filament\Pages\Page;

class CustomFields extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Customers';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.customers.custom-fields';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-plus';

    protected static ?string $navigationLabel = 'Custom Fields';

    public function getTitle(): string
    {
        return 'Custom Fields';
    }
}
