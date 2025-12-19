<?php

namespace App\Filament\Pages\System\Users;

use Filament\Pages\Page;

class API extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.system.users.api';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'API';

    public function getTitle(): string
    {
        return 'API';
    }
}
