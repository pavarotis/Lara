<?php

namespace App\Filament\Pages\CMS\Blog;

use Filament\Pages\Page;

class Comments extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 13;

    protected string $view = 'filament.pages.cms.blog.comments';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Blog Comments';

    public function getTitle(): string
    {
        return 'Blog Comments';
    }
}
