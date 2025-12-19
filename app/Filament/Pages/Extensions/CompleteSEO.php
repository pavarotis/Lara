<?php

namespace App\Filament\Pages\Extensions;

use Filament\Pages\Page;

class CompleteSEO extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Extensions';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.extensions.complete-seo';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-magnifying-glass';

    protected static ?string $navigationLabel = 'Complete SEO';

    public function getTitle(): string
    {
        return 'Complete SEO';
    }
}
