<?php

namespace App\Filament\Pages\Customers;

use Filament\Pages\Page;

class CustomerApprovals extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Customers';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.customers.customer-approvals';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-check-circle';

    protected static ?string $navigationLabel = 'Customer Approvals';

    public function getTitle(): string
    {
        return 'Customer Approvals';
    }
}
