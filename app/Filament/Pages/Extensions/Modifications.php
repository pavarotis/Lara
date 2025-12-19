<?php

namespace App\Filament\Pages\Extensions;

use Filament\Pages\Page;

class Modifications extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Extensions';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.extensions.modifications';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Modifications';

    public function getTitle(): string
    {
        return 'Modifications';
    }
}
