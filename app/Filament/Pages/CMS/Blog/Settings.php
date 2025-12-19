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

    public function getTitle(): string
    {
        return 'Blog Settings';
    }
}
