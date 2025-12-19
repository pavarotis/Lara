<?php

namespace App\Filament\Pages\Sales\GiftVouchers;

use Filament\Pages\Page;

class GiftVouchers extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.sales.gift-vouchers.gift-vouchers';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationLabel = 'Gift Vouchers';

    public function getTitle(): string
    {
        return 'Gift Vouchers';
    }
}
