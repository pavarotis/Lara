<?php

namespace App\Filament\Pages\CMS\Blog;

use Filament\Pages\Page;

class Categories extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 11;

    protected string $view = 'filament.pages.cms.blog.categories';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationLabel = 'Blog Categories';

    /**
     * Use a custom slug to avoid clashing with the legacy /admin/categories routes.
     * This will register the Filament page at /admin/blog-categories with route name filament.admin.pages.blog-categories.
     */
    public static function getSlug(?\Filament\Panel $panel = null): string
    {
        return 'blog-categories';
    }

    /**
     * Override route name to match custom slug
     */
    public static function getRouteName(?\Filament\Panel $panel = null): string
    {
        $panel ??= \Filament\Facades\Filament::getPanel('admin');
        $slug = static::getSlug($panel);

        return $panel->generateRouteName('pages.'.$slug);
    }

    public function getTitle(): string
    {
        return 'Blog Categories';
    }
}
