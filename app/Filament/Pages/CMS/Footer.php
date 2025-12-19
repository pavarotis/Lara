<?php

namespace App\Filament\Pages\CMS;

use Filament\Pages\Page;

class Footer extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 7;

    protected string $view = 'filament.pages.cms.footer';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Footer';

    public function getTitle(): string
    {
        return 'Footer Management';
    }
}
