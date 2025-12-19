<?php

namespace App\Filament\Pages\Sales;

use Filament\Pages\Page;

class Returns extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.sales.returns';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $navigationLabel = 'Returns';

    public function getTitle(): string
    {
        return 'Returns';
    }
}
