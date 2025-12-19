<?php

namespace App\Filament\Pages\System\Users;

use Filament\Pages\Page;

class UserGroups extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.system.users.user-groups';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'User Groups';

    public function getTitle(): string
    {
        return 'User Groups';
    }
}
