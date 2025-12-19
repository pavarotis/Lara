<?php

namespace App\Filament\Pages\CMS;

use Filament\Pages\Page;

class Modules extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 8;

    protected string $view = 'filament.pages.cms.modules';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $navigationLabel = 'Modules';

    public function getTitle(): string
    {
        return 'Modules';
    }
}
