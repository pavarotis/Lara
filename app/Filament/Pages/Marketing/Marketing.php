<?php

namespace App\Filament\Pages\Marketing;

use Filament\Pages\Page;

class Marketing extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.marketing.marketing';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationLabel = 'Marketing Dashboard';

    public function getTitle(): string
    {
        return 'Marketing';
    }
}
