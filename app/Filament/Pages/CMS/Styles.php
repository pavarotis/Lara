<?php

declare(strict_types=1);

namespace App\Filament\Pages\CMS;

use App\Domain\Businesses\Models\Business;
use App\Domain\Themes\Models\ThemePreset;
use App\Domain\Themes\Services\GetThemeTokensService;
use App\Domain\Themes\Services\UpdateThemeTokensService;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class Styles extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.cms.styles';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-paint-brush';

    protected static ?string $navigationLabel = 'Styles';

    public ?array $data = [];

    public function mount(): void
    {
        $business = Business::active()->first();
        if (! $business) {
            Notification::make()
                ->danger()
                ->title('No active business found')
                ->send();

            return;
        }

        $getThemeTokensService = app(GetThemeTokensService::class);
        $tokens = $getThemeTokensService->getTokens($business);

        // Extract color values (ensure they are strings, not arrays)
        $colors = $tokens['colors'] ?? [];
        $primaryColor = is_array($colors['primary'] ?? null) ? ($colors['primary']['DEFAULT'] ?? ($colors['primary'][0] ?? '#3b82f6')) : (string) ($colors['primary'] ?? '#3b82f6');
        $accentColor = is_array($colors['accent'] ?? null) ? ($colors['accent']['DEFAULT'] ?? ($colors['accent'][0] ?? '#10b981')) : (string) ($colors['accent'] ?? '#10b981');
        $backgroundColor = is_array($colors['background'] ?? null) ? ($colors['background']['DEFAULT'] ?? ($colors['background'][0] ?? '#ffffff')) : (string) ($colors['background'] ?? '#ffffff');
        $textColor = is_array($colors['text'] ?? null) ? ($colors['text']['DEFAULT'] ?? ($colors['text'][0] ?? '#1f2937')) : (string) ($colors['text'] ?? '#1f2937');

        // Extract font values (ensure they are strings)
        $fonts = $tokens['fonts'] ?? [];
        $primaryFont = is_array($fonts['primary'] ?? null) ? ($fonts['primary']['family'] ?? 'Outfit') : (string) ($fonts['primary'] ?? 'Outfit');
        $secondaryFont = is_array($fonts['secondary'] ?? null) ? ($fonts['secondary']['family'] ?? 'Outfit') : (string) ($fonts['secondary'] ?? 'Outfit');

        // Also check for 'heading' and 'body' font keys (from presets)
        if (empty($primaryFont) && isset($fonts['heading']['family'])) {
            $primaryFont = $fonts['heading']['family'];
        }
        if (empty($secondaryFont) && isset($fonts['body']['family'])) {
            $secondaryFont = $fonts['body']['family'];
        }

        $this->form->fill([
            'preset' => $business->settings['theme_preset'] ?? null,
            'header_variant' => $business->settings['header_variant'] ?? 'minimal',
            'footer_variant' => $business->settings['footer_variant'] ?? 'simple',
            'token_overrides' => [
                'colors' => [
                    'primary' => $primaryColor,
                    'accent' => $accentColor,
                    'background' => $backgroundColor,
                    'text' => $textColor,
                ],
                'fonts' => [
                    'primary' => $primaryFont,
                    'secondary' => $secondaryFont,
                ],
            ],
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('preset')
                    ->label('Theme Preset')
                    ->options(ThemePreset::pluck('name', 'slug')->toArray())
                    ->searchable()
                    ->live()
                    ->helperText('Select a theme preset to apply default styles'),
                Select::make('header_variant')
                    ->label('Header Variant')
                    ->options(array_column(config('header_variants'), 'name', 'key'))
                    ->required()
                    ->helperText('Choose the header layout style'),
                Select::make('footer_variant')
                    ->label('Footer Variant')
                    ->options(array_column(config('footer_variants'), 'name', 'key'))
                    ->required()
                    ->helperText('Choose the footer layout style'),
                Section::make('Color Overrides')
                    ->schema([
                        ColorPicker::make('token_overrides.colors.primary')
                            ->label('Primary Color'),
                        ColorPicker::make('token_overrides.colors.accent')
                            ->label('Accent Color'),
                        ColorPicker::make('token_overrides.colors.background')
                            ->label('Background Color'),
                        ColorPicker::make('token_overrides.colors.text')
                            ->label('Text Color'),
                    ])
                    ->collapsible(),
                Section::make('Font Overrides')
                    ->schema([
                        TextInput::make('token_overrides.fonts.primary')
                            ->label('Primary Font')
                            ->placeholder('e.g., Outfit, Inter'),
                        TextInput::make('token_overrides.fonts.secondary')
                            ->label('Secondary Font')
                            ->placeholder('e.g., Outfit, Inter'),
                    ])
                    ->collapsible(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $business = Business::active()->first();

        if (! $business) {
            Notification::make()
                ->danger()
                ->title('No active business found')
                ->send();

            return;
        }

        // Save theme settings
        $updateService = app(UpdateThemeTokensService::class);
        $updateService->execute($business, $data);

        // Clear cache to apply changes immediately
        \Illuminate\Support\Facades\Cache::flush();

        Notification::make()
            ->success()
            ->title('Theme settings saved successfully')
            ->body('The page will refresh to show your changes.')
            ->send();

        // Redirect to refresh the page and show changes
        $this->redirect(route('filament.admin.pages.styles'));
    }

    public function getTitle(): string
    {
        return 'Theme Settings';
    }
}
