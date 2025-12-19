<?php

namespace App\Filament\Pages\CMS;

use Filament\Pages\Page;

class Skins extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.cms.skins';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $navigationLabel = 'Skins';

    public function getTitle(): string
    {
        return 'Skins';
    }
}
