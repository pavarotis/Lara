<?php

namespace App\Filament\Pages\System;

use Filament\Pages\Page;

class Settings extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.system.settings';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Settings';

    /**
     * Use a custom slug to avoid clashing with the legacy /admin/settings route.
     * This will register the Filament page at /admin/system-settings with route name filament.admin.pages.system-settings.
     */
    public static function getSlug(?\Filament\Panel $panel = null): string
    {
        return 'system-settings';
    }

    public function getTitle(): string
    {
        return 'Settings';
    }
}
