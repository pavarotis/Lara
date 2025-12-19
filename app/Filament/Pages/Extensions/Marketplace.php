<?php

namespace App\Filament\Pages\Extensions;

use Filament\Pages\Page;

class Marketplace extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Extensions';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.extensions.marketplace';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Marketplace';

    public function getTitle(): string
    {
        return 'Marketplace';
    }
}
