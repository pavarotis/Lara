<?php

namespace App\Filament\Pages\CMS;

use Filament\Pages\Page;

class Header extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.pages.cms.header';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Header';

    public function getTitle(): string
    {
        return 'Header Management';
    }
}
