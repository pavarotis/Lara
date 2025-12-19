<?php

namespace App\Filament\Pages\Extensions;

use Filament\Pages\Page;

class Events extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Extensions';

    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.pages.extensions.events';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Events';

    public function getTitle(): string
    {
        return 'Events';
    }
}
