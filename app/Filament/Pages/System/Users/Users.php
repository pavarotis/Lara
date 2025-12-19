<?php

namespace App\Filament\Pages\System\Users;

use Filament\Pages\Page;

class Users extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.system.users.users';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Users';

    /**
     * Use a custom slug to avoid clashing with the Filament Users resource routes.
     * This will register the page at /admin/system-users with route name
     * filament.admin.pages.system-users.
     */
    public static function getSlug(?\Filament\Panel $panel = null): string
    {
        return 'system-users';
    }

    public function getTitle(): string
    {
        return 'Users';
    }
}
