<?php

namespace App\Filament\Pages\Marketing;

use Filament\Pages\Page;

class Mail extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.marketing.mail';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Mail';

    public function getTitle(): string
    {
        return 'Mail';
    }
}
