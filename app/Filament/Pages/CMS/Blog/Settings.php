<?php

declare(strict_types=1);

namespace App\Filament\Pages\CMS\Blog;

use App\Domain\Businesses\Models\Business;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = 'CMS / Blog';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.cms.blog.settings';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Blog Settings';

    public ?Business $business = null;

    public ?array $data = [];

    /**
     * Use a custom slug to avoid clashing with other routes.
     */
    public static function getSlug(?\Filament\Panel $panel = null): string
    {
        return 'blog-settings';
    }

    public function mount(): void
    {
        $this->business = Business::active()->first();
        if (! $this->business) {
            Notification::make()
                ->title('No active business found')
                ->danger()
                ->send();

            return;
        }

        $settings = $this->business->getSetting('blog', []);

        $this->form->fill([
            'posts_per_page' => $settings['posts_per_page'] ?? 10,
            'enable_comments' => $settings['enable_comments'] ?? true,
            'moderate_comments' => $settings['moderate_comments'] ?? true,
            'allow_guest_comments' => $settings['allow_guest_comments'] ?? false,
            'show_author' => $settings['show_author'] ?? true,
            'show_date' => $settings['show_date'] ?? true,
            'show_categories' => $settings['show_categories'] ?? true,
            'show_tags' => $settings['show_tags'] ?? false,
            'excerpt_length' => $settings['excerpt_length'] ?? 150,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Display Settings')
                    ->description('Configure how blog posts are displayed')
                    ->columns(2)
                    ->schema([
                        TextInput::make('posts_per_page')
                            ->label('Posts Per Page')
                            ->numeric()
                            ->required()
                            ->default(10)
                            ->helperText('Number of blog posts to show per page'),
                        TextInput::make('excerpt_length')
                            ->label('Excerpt Length')
                            ->numeric()
                            ->required()
                            ->default(150)
                            ->helperText('Character length for post excerpts'),
                        Toggle::make('show_author')
                            ->label('Show Author')
                            ->default(true)
                            ->helperText('Display author name on blog posts'),
                        Toggle::make('show_date')
                            ->label('Show Date')
                            ->default(true)
                            ->helperText('Display publish date on blog posts'),
                        Toggle::make('show_categories')
                            ->label('Show Categories')
                            ->default(true)
                            ->helperText('Display categories on blog posts'),
                        Toggle::make('show_tags')
                            ->label('Show Tags')
                            ->default(false)
                            ->helperText('Display tags on blog posts (if implemented)'),
                    ]),
                Section::make('Comment Settings')
                    ->description('Configure comment behavior')
                    ->columns(2)
                    ->schema([
                        Toggle::make('enable_comments')
                            ->label('Enable Comments')
                            ->default(true)
                            ->helperText('Allow comments on blog posts'),
                        Toggle::make('moderate_comments')
                            ->label('Moderate Comments')
                            ->default(true)
                            ->helperText('Require approval before comments are published'),
                        Toggle::make('allow_guest_comments')
                            ->label('Allow Guest Comments')
                            ->default(false)
                            ->helperText('Allow comments from non-registered users'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        if (! $this->business) {
            return;
        }

        $data = $this->form->getState();
        $settings = $this->business->settings ?? [];
        $settings['blog'] = $data;
        $this->business->update(['settings' => $settings]);

        Notification::make()
            ->title('Blog settings saved')
            ->success()
            ->send();
    }

    public function getTitle(): string
    {
        return 'Blog Settings';
    }
}
