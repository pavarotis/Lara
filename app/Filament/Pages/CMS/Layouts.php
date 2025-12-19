<?php

namespace App\Filament\Pages\CMS;

use Filament\Pages\Page;

class Layouts extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.cms.layouts';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Layouts';

    public function getTitle(): string
    {
        return 'Layouts';
    }
}
