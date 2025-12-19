<?php

namespace App\Filament\Pages\Marketing;

use Filament\Pages\Page;

class Coupons extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.marketing.coupons';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'Coupons';

    public function getTitle(): string
    {
        return 'Coupons';
    }
}
