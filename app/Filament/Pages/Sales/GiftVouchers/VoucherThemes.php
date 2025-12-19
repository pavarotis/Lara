<?php

namespace App\Filament\Pages\Sales\GiftVouchers;

use Filament\Pages\Page;

class VoucherThemes extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.sales.gift-vouchers.voucher-themes';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $navigationLabel = 'Voucher Themes';

    public function getTitle(): string
    {
        return 'Voucher Themes';
    }
}
