<?php

namespace App\Filament\Pages\CMS;

use Filament\Pages\Page;

class Styles extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.cms.styles';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-paint-brush';

    protected static ?string $navigationLabel = 'Styles';

    public function getTitle(): string
    {
        return 'Styles';
    }
}
