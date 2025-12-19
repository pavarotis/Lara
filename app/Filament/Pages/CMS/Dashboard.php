<?php

namespace App\Filament\Pages\CMS;

use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.cms.dashboard';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Dashboard';

    public function getTitle(): string
    {
        return 'CMS Dashboard';
    }
}
