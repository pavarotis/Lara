<?php

namespace App\Filament\Pages\Extensions;

use App\Domain\Businesses\Models\Business;
use App\Domain\Seo\Models\Redirect;
use App\Domain\Seo\Services\GenerateJsonLdService;
use App\Domain\Seo\Services\GetSitemapService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema as SchemaFacade;

class CompleteSEO extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string|\UnitEnum|null $navigationGroup = 'Extensions';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.extensions.complete-seo';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-magnifying-glass';

    protected static ?string $navigationLabel = 'Complete SEO';

    public ?array $data = [];

    public ?int $editingRedirectId = null;

    public ?array $redirectData = [];

    public string $activeTab = 'global';

    public function mount(): void
    {
        $getSettings = app(\App\Domain\Settings\Services\GetSettingsService::class);

        $this->form->fill([
            'meta_title' => $getSettings->get('seo.meta_title', config('app.name')),
            'meta_description' => $getSettings->get('seo.meta_description', ''),
            'meta_keywords' => $getSettings->get('seo.meta_keywords', ''),
            'og_title' => $getSettings->get('seo.og_title', ''),
            'og_description' => $getSettings->get('seo.og_description', ''),
            'og_image' => $getSettings->get('seo.og_image', ''),
            'twitter_card' => $getSettings->get('seo.twitter_card', 'summary_large_image'),
            'twitter_site' => $getSettings->get('seo.twitter_site', ''),
            'robots_index' => filter_var($getSettings->get('seo.robots_index', '1'), FILTER_VALIDATE_BOOLEAN),
            'robots_follow' => filter_var($getSettings->get('seo.robots_follow', '1'), FILTER_VALIDATE_BOOLEAN),
            'canonical_url' => $getSettings->get('seo.canonical_url', config('app.url')),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic SEO')
                    ->components([
                        TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(60)
                            ->helperText('Recommended: 50-60 characters')
                            ->required(),
                        Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->maxLength(160)
                            ->rows(3)
                            ->helperText('Recommended: 150-160 characters'),
                        Textarea::make('meta_keywords')
                            ->label('Meta Keywords')
                            ->rows(2)
                            ->helperText('Comma-separated keywords'),
                    ]),

                Section::make('Open Graph (Facebook)')
                    ->components([
                        TextInput::make('og_title')
                            ->label('OG Title')
                            ->maxLength(60),
                        Textarea::make('og_description')
                            ->label('OG Description')
                            ->maxLength(200)
                            ->rows(2),
                        TextInput::make('og_image')
                            ->label('OG Image URL')
                            ->url()
                            ->helperText('Recommended: 1200x630px'),
                    ])
                    ->collapsible(),

                Section::make('Twitter Card')
                    ->components([
                        TextInput::make('twitter_card')
                            ->label('Twitter Card Type')
                            ->default('summary_large_image')
                            ->helperText('summary, summary_large_image'),
                        TextInput::make('twitter_site')
                            ->label('Twitter Site')
                            ->helperText('@username'),
                    ])
                    ->collapsible(),

                Section::make('Robots & Canonical')
                    ->components([
                        Toggle::make('robots_index')
                            ->label('Allow Indexing')
                            ->default(true)
                            ->helperText('Allow search engines to index your site'),
                        Toggle::make('robots_follow')
                            ->label('Follow Links')
                            ->default(true)
                            ->helperText('Allow search engines to follow links'),
                        TextInput::make('canonical_url')
                            ->label('Canonical URL')
                            ->url()
                            ->default(config('app.url'))
                            ->helperText('Default canonical URL for your site'),
                    ])
                    ->collapsible(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            $type = in_array($key, ['robots_index', 'robots_follow']) ? 'boolean' : 'string';
            $this->updateSetting("seo.{$key}", $value, $type);
        }

        Notification::make()
            ->title('SEO Settings Saved')
            ->success()
            ->send();
    }

    public function generateSitemap(): void
    {
        try {
            $business = Business::active()->first();
            if (! $business) {
                Notification::make()
                    ->title('Error')
                    ->danger()
                    ->body('No active business found.')
                    ->send();

                return;
            }

            $sitemapService = app(GetSitemapService::class);
            $xml = $sitemapService->generate($business);

            // Store in cache for preview
            Cache::put('seo.sitemap.preview', $xml, now()->addHour());

            Notification::make()
                ->title('Sitemap Generated')
                ->success()
                ->body('Sitemap has been generated successfully.')
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->danger()
                ->body('Failed to generate sitemap: '.$e->getMessage())
                ->send();
        }
    }

    public function getSitemapPreview(): ?string
    {
        return Cache::get('seo.sitemap.preview');
    }

    public function getJsonLdPreview(): ?array
    {
        try {
            $business = Business::active()->first();
            if (! $business) {
                return null;
            }

            $jsonLdService = app(GenerateJsonLdService::class);

            return $jsonLdService->forBusiness($business);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getTitle(): string
    {
        return 'Complete SEO';
    }

    private function updateSetting(string $key, mixed $value, string $type = 'string'): void
    {
        app(\App\Domain\Settings\Services\UpdateSettingsService::class)->execute($key, $value, $type, 'seo');
    }

    // URL Redirection Management
    public function table(Table $table): Table
    {
        $business = Business::active()->first();

        // Check if redirects table exists
        if (! SchemaFacade::hasTable('redirects')) {
            return $table
                ->query(fn () => Redirect::query()->whereRaw('1 = 0'))
                ->columns([])
                ->emptyStateHeading('Redirects Table Not Found')
                ->emptyStateDescription('Please run the migration: php artisan migrate');
        }

        return $table
            ->query(Redirect::query()->forBusiness($business?->id))
            ->columns([
                TextColumn::make('from_url')
                    ->label('From URL')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('to_url')
                    ->label('To URL')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '301' => 'success',
                        '302' => 'warning',
                        default => 'gray',
                    }),
                ToggleColumn::make('is_active')
                    ->label('Active'),
                TextColumn::make('hits')
                    ->label('Hits')
                    ->sortable(),
                TextColumn::make('last_hit_at')
                    ->label('Last Hit')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->form([
                        TextInput::make('from_url')
                            ->label('From URL')
                            ->required()
                            ->helperText('The old URL that should redirect'),
                        TextInput::make('to_url')
                            ->label('To URL')
                            ->required()
                            ->url()
                            ->helperText('The new URL to redirect to'),
                        Select::make('type')
                            ->label('Redirect Type')
                            ->options([
                                '301' => '301 - Permanent Redirect',
                                '302' => '302 - Temporary Redirect',
                            ])
                            ->default('301')
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(2),
                    ])
                    ->fillForm(fn (Redirect $record): array => [
                        'from_url' => $record->from_url,
                        'to_url' => $record->to_url,
                        'type' => $record->type,
                        'is_active' => $record->is_active,
                        'notes' => $record->notes,
                    ])
                    ->action(function (Redirect $record, array $data) {
                        $record->update($data);
                        Notification::make()
                            ->title('Redirect Updated')
                            ->success()
                            ->send();
                    }),
                Action::make('delete')
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Redirect $record) {
                        $record->delete();
                        Notification::make()
                            ->title('Redirect Deleted')
                            ->success()
                            ->send();
                    }),
            ])
            ->headerActions([
                Action::make('create')
                    ->label('Add Redirect')
                    ->icon('heroicon-o-plus')
                    ->form([
                        TextInput::make('from_url')
                            ->label('From URL')
                            ->required()
                            ->helperText('The old URL that should redirect'),
                        TextInput::make('to_url')
                            ->label('To URL')
                            ->required()
                            ->url()
                            ->helperText('The new URL to redirect to'),
                        Select::make('type')
                            ->label('Redirect Type')
                            ->options([
                                '301' => '301 - Permanent Redirect',
                                '302' => '302 - Temporary Redirect',
                            ])
                            ->default('301')
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(2),
                    ])
                    ->action(function (array $data) use ($business) {
                        Redirect::create([
                            'business_id' => $business?->id,
                            'from_url' => $data['from_url'],
                            'to_url' => $data['to_url'],
                            'type' => $data['type'],
                            'is_active' => $data['is_active'] ?? true,
                            'notes' => $data['notes'] ?? null,
                        ]);

                        Notification::make()
                            ->title('Redirect Created')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
