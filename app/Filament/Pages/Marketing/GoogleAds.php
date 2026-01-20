<?php

namespace App\Filament\Pages\Marketing;

use Filament\Pages\Page;

class GoogleAds extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.marketing.google-ads';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?string $navigationLabel = 'Google Ads';

    // Note: This is a settings/integration page, not a Resource

    public function getTitle(): string
    {
        return 'Google Ads';
    }
}
