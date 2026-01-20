<?php

namespace App\Filament\Pages\CMS\Blog;

use Filament\Pages\Page;

class Settings extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.cms.blog.settings';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Blog Settings';

    /**
     * Use a custom slug to avoid clashing with other routes.
     * This will register the Filament page at /admin/blog-settings with route name filament.admin.pages.blog-settings.
     */
    public static function getSlug(?\Filament\Panel $panel = null): string
    {
        return 'blog-settings';
    }

    public function getTitle(): string
    {
        return 'Blog Settings';
    }
}
