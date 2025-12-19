<?php

namespace App\Filament\Pages\CMS;

use Filament\Pages\Page;

class Variables extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.cms.variables';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-variable';

    protected static ?string $navigationLabel = 'Variables';

    public function getTitle(): string
    {
        return 'Variables';
    }
}
