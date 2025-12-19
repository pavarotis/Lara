<?php

namespace App\Filament\Pages\CMS\Blog;

use Filament\Pages\Page;

class Posts extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 12;

    protected string $view = 'filament.pages.cms.blog.posts';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Blog Posts';

    public function getTitle(): string
    {
        return 'Blog Posts';
    }
}
