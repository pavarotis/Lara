<?php

namespace App\Filament\Pages\Catalog;

use Filament\Pages\Page;

class RecurringProfiles extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.catalog.recurring-profiles';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationLabel = 'Recurring Profiles';

    public function getTitle(): string
    {
        return 'Recurring Profiles';
    }
}
