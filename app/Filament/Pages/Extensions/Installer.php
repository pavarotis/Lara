<?php

namespace App\Filament\Pages\Extensions;

use Filament\Pages\Page;

class Installer extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Extensions';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.extensions.installer';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-down-circle';

    protected static ?string $navigationLabel = 'Installer';

    public function getTitle(): string
    {
        return 'Installer';
    }
}
