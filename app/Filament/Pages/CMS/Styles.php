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

        $this->form->fill([
            'preset' => $business->settings['theme_preset'] ?? null,
            'header_variant' => $business->settings['header_variant'] ?? 'minimal',
            'footer_variant' => $business->settings['footer_variant'] ?? 'simple',
            'token_overrides' => [
                'colors' => [
                    'primary' => $tokens['colors']['primary'] ?? '#3b82f6',
                    'accent' => $tokens['colors']['accent'] ?? '#10b981',
                    'background' => $tokens['colors']['background'] ?? '#ffffff',
                    'text' => $tokens['colors']['text'] ?? '#1f2937',
                ],
                'fonts' => [
                    'primary' => $tokens['fonts']['primary'] ?? 'Outfit',
                    'secondary' => $tokens['fonts']['secondary'] ?? 'Outfit',
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

        Notification::make()
            ->success()
            ->title('Theme settings saved successfully')
            ->send();
    }

    public function getTitle(): string
    {
        return 'Theme Settings';
    }
}
