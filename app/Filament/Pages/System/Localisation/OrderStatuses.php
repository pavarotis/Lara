<?php

namespace App\Filament\Pages\System\Localisation;

use Filament\Pages\Page;

class OrderStatuses extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.system.localisation.order-statuses';

    public function getTitle(): string
    {
        return 'Order Statuses';
    }
}
