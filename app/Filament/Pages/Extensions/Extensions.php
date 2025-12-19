<?php

namespace App\Filament\Pages\Extensions;

use Filament\Pages\Page;

class Extensions extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Extensions';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.extensions.extensions';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $navigationLabel = 'Extensions';

    public function getTitle(): string
    {
        return 'Extensions';
    }
}
