<?php

namespace App\Filament\Pages\System\Localisation;

use Filament\Pages\Page;

class OrderStatuses extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 9;

    protected string $view = 'filament.pages.system.localisation.order-statuses';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Order Statuses';

    public function getTitle(): string
    {
        return 'Order Statuses';
    }
}
