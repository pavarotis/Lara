<?php

namespace App\Filament\Pages\CMS;

use App\Domain\Businesses\Models\Business;
use App\Domain\Variables\Models\Variable;
use App\Support\VariableHelper;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Variables extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.cms.variables';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-variable';

    protected static ?string $navigationLabel = 'CMS Variables';

    public string $activeTab = 'general';

    public string $typographyLang = 'en';

    public ?array $data = [];

    public bool $googleFontsApiAvailable = true;

    public ?string $googleFontsApiError = null;

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

        // Check Google Fonts API connection
        $this->checkGoogleFontsApiConnection();

        // Load existing variables for each tab
        $this->loadVariables($business);
    }

    /**
     * Check if Google Fonts API is available (with detailed error reporting)
     */
    public function checkGoogleFontsApiConnection(): void
    {
        // Clear previous error
        $this->googleFontsApiError = null;

        try {
            // Build API URL with optional API key
            [$url, $params] = $this->buildGoogleFontsApiUrl(['sort' => 'popularity']);

            // Quick check with short timeout
            $response = Http::timeout(5)->get($url, $params);

            if ($response->successful()) {
                $data = $response->json();

                // Check if we got valid data
                if (isset($data['items']) && is_array($data['items']) && count($data['items']) > 0) {
                    $this->googleFontsApiAvailable = true;
                    $this->googleFontsApiError = null;

                    // Clear connection status cache on success
                    Cache::forget('google_fonts_api_connection_status');

                    return;
                } else {
                    // API responded but no data
                    $this->googleFontsApiAvailable = false;
                    $this->googleFontsApiError = 'API returned empty data. Check if API key is valid or if API is enabled.';
                }
            } else {
                // API returned error status
                $statusCode = $response->status();
                $errorBody = $response->json();

                $this->googleFontsApiAvailable = false;

                if ($statusCode === 403) {
                    $this->googleFontsApiError = 'API key is invalid or API is not enabled. Check your Google Cloud Console.';
                } elseif ($statusCode === 400) {
                    $this->googleFontsApiError = 'Invalid API request. Check your API key configuration.';
                } elseif ($statusCode === 429) {
                    $this->googleFontsApiError = 'Rate limit exceeded. Please wait a few minutes and try again.';
                } else {
                    $errorMessage = $errorBody['error']['message'] ?? 'Unknown error';
                    $this->googleFontsApiError = "API Error ({$statusCode}): {$errorMessage}";
                }
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Network/connection error
            $this->googleFontsApiAvailable = false;
            $this->googleFontsApiError = 'Cannot connect to Google Fonts API. Check your internet connection.';
            Log::warning('Google Fonts API connection failed: '.$e->getMessage());
        } catch (\Exception $e) {
            // Other errors
            $this->googleFontsApiAvailable = false;
            $this->googleFontsApiError = 'Connection error: '.$e->getMessage();
            Log::warning('Google Fonts API check failed: '.$e->getMessage());
        }
    }

    /**
     * Manual refresh of Google Fonts API connection check
     */
    public function refreshGoogleFontsApiConnection(): void
    {
        // Clear cache to force fresh check
        Cache::forget('google_fonts_api_connection_status');

        // Re-check connection
        $this->checkGoogleFontsApiConnection();

        // Show notification with result
        if ($this->googleFontsApiAvailable) {
            Notification::make()
                ->success()
                ->title('Google Fonts API Connected')
                ->body('Successfully connected to Google Fonts API.')
                ->send();
        } else {
            Notification::make()
                ->warning()
                ->title('Google Fonts API Unavailable')
                ->body($this->googleFontsApiError ?? 'Cannot connect to Google Fonts API.')
                ->send();
        }
    }

    /**
     * Get variable value from database (like Journal theme approach)
     * This method is called dynamically when needed, not loaded all at once
     * Uses caching to avoid repeated database queries
     */
    protected function getVariableValue(string $key, mixed $default = null): mixed
    {
        $business = Business::active()->first();
        if (! $business) {
            return $default;
        }

        $cacheKey = "cms_variable:{$business->id}:{$key}";

        return Cache::remember($cacheKey, 3600, function () use ($business, $key, $default) {
            $variable = Variable::forBusiness($business->id)
                ->where('key', $key)
                ->first();

            if (! $variable) {
                return $default;
            }

            // For JSON type, decode it
            if ($variable->type === 'json') {
                return json_decode($variable->value, true) ?? $default;
            }

            // For number type, return as number
            if ($variable->type === 'number') {
                return is_numeric($variable->value) ? (str_contains($variable->value, '.') ? (float) $variable->value : (int) $variable->value) : $default;
            }

            // For boolean type
            if ($variable->type === 'boolean') {
                return filter_var($variable->value, FILTER_VALIDATE_BOOLEAN);
            }

            return $variable->value ?? $default;
        });
    }

    /**
     * Load only JSON array variables (Repeaters) that need to be loaded upfront
     * All other simple values are loaded dynamically via getVariableValue() in form field defaults
     * This approach is similar to Journal theme - values are called from DB when needed
     */
    protected function loadVariables(Business $business): void
    {
        // Only load JSON arrays (Repeaters) that need to be available for form structure
        // Simple string/number values are loaded dynamically via default() closures in form fields
        $this->form->fill([
            'general' => [
                // Load JSON arrays for Repeaters
                'color_styles' => $this->getVariableValue('general.color_styles', $this->getDefaultColorStyles()),
                'shadow_presets' => $this->getVariableValue('general.shadow_presets', $this->getDefaultShadowPresets()),
                'border_radius' => $this->getVariableValue('general.border_radius', $this->getDefaultBorderRadius()),
                'selected_color_style' => null,
            ],
            'si' => [
                'length_classes' => $this->getVariableValue('si.length_classes', $this->getDefaultLengthClasses()),
                'weight_classes' => $this->getVariableValue('si.weight_classes', $this->getDefaultWeightClasses()),
            ],
            'typography_en' => [],
            'typography_gr' => [],
            'legacy' => [],
        ]);
    }

    protected function getDefaultColorStyles(): array
    {
        return [];
    }

    protected function getDefaultShadowPresets(): array
    {
        return [];
    }

    protected function getDefaultBorderRadius(): array
    {
        return [];
    }

    protected function getDefaultLengthClasses(): array
    {
        return [];
    }

    /**
     * Get default weight classes
     */
    protected function getDefaultWeightClasses(): array
    {
        return [];
    }

    public function updatedActiveTab(): void
    {
        // Check API connection when switching to typography tab
        if ($this->activeTab === 'typography') {
            $this->checkGoogleFontsApiConnection();
        }

        // Reload form data when tab changes
        $business = Business::active()->first();
        if ($business) {
            $this->loadVariables($business);
        }
    }

    public function updatedTypographyLang(): void
    {
        // Reload form data when typography language changes
        $business = Business::active()->first();
        if ($business) {
            $this->loadVariables($business);
        }
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                // General tab form
                Section::make('Breakpoints')
                    ->schema([
                        Grid::make(2)
                            ->schema($this->createFieldsFromConfig('breakpoints')),
                    ])
                    ->visible(fn () => $this->activeTab === 'general')
                    ->collapsible()
                    ->collapsed(),

                Section::make('Color Styles')
                    ->description('Create and manage color style presets. Select a style below to automatically apply its colors.')
                    ->schema([
                        Repeater::make('general.color_styles')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Style Name')
                                    ->required()
                                    ->helperText('Name for this color style preset'),
                                Grid::make(3)
                                    ->schema([
                                        ColorPicker::make('color_primary')
                                            ->label('Primary Color')
                                            ->required(),
                                        ColorPicker::make('color_secondary')
                                            ->label('Secondary Color')
                                            ->required(),
                                        ColorPicker::make('color_accent')
                                            ->label('Accent Color')
                                            ->required(),
                                        ColorPicker::make('color_success')
                                            ->label('Success Color')
                                            ->required(),
                                        ColorPicker::make('color_warning')
                                            ->label('Warning Color')
                                            ->required(),
                                        ColorPicker::make('color_danger')
                                            ->label('Danger Color')
                                            ->required(),
                                        ColorPicker::make('color_info')
                                            ->label('Info Color')
                                            ->required(),
                                        ColorPicker::make('color_background')
                                            ->label('Background Color')
                                            ->required(),
                                        ColorPicker::make('color_text')
                                            ->label('Text Color')
                                            ->required(),
                                    ]),
                            ])
                            ->columns(1)
                            ->defaultItems(5)
                            ->itemLabel(fn (array $state): ?string => ($state['name'] ?? 'New Color Style'))
                            ->reorderable()
                            ->deletable()
                            ->addActionLabel('Add Color Style'),
                    ])
                    ->visible(fn () => $this->activeTab === 'general')
                    ->collapsible()
                    ->collapsed(),

                Section::make('Color Tokens')
                    ->description('Select a color style to apply, or manually customize individual colors.')
                    ->schema([
                        Select::make('general.selected_color_style')
                            ->label('Apply Color Style')
                            ->options(function (callable $get) {
                                $styles = $get('general.color_styles') ?? [];
                                $options = [];
                                foreach ($styles as $index => $style) {
                                    $options[$index] = $style['name'] ?? 'Style '.($index + 1);
                                }

                                return $options;
                            })
                            ->placeholder('Select a style to apply colors...')
                            ->helperText('Choose a color style to automatically fill the color fields below')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state !== null) {
                                    $styles = $get('general.color_styles') ?? [];
                                    if (isset($styles[$state])) {
                                        $style = $styles[$state];
                                        $set('general.color_primary', $style['color_primary'] ?? $this->getVariableValue('general.color.primary', ''));
                                        $set('general.color_secondary', $style['color_secondary'] ?? $this->getVariableValue('general.color.secondary', ''));
                                        $set('general.color_accent', $style['color_accent'] ?? $this->getVariableValue('general.color.accent', ''));
                                        $set('general.color_success', $style['color_success'] ?? $this->getVariableValue('general.color.success', ''));
                                        $set('general.color_warning', $style['color_warning'] ?? $this->getVariableValue('general.color.warning', ''));
                                        $set('general.color_danger', $style['color_danger'] ?? $this->getVariableValue('general.color.danger', ''));
                                        $set('general.color_info', $style['color_info'] ?? $this->getVariableValue('general.color.info', ''));
                                        $set('general.color_background', $style['color_background'] ?? $this->getVariableValue('general.color.background', ''));
                                        $set('general.color_text', $style['color_text'] ?? $this->getVariableValue('general.color.text', ''));
                                    }
                                }
                            })
                            ->columnSpanFull(),
                        Grid::make(3)
                            ->schema($this->createFieldsFromConfig('colors')),
                    ])
                    ->visible(fn () => $this->activeTab === 'general')
                    ->collapsible(),

                Section::make('Shadow Presets')
                    ->description('Configure shadow presets for different UI elements. Use these in your CSS with var(--shadow-*) or directly in Tailwind classes.')
                    ->schema([
                        Repeater::make('general.shadow_presets')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Preset Name')
                                            ->required(),
                                        TextInput::make('value')
                                            ->label('Shadow Value')
                                            ->required()
                                            ->placeholder('0 1px 2px 0 rgb(0 0 0 / 0.05)'),
                                    ]),
                            ])
                            ->columns(1)
                            ->defaultItems(14)
                            ->itemLabel(fn (array $state): ?string => ($state['name'] ?? 'New Shadow Preset'))
                            ->reorderable()
                            ->deletable()
                            ->addActionLabel('Add Shadow Preset'),
                    ])
                    ->visible(fn () => $this->activeTab === 'general')
                    ->collapsible()
                    ->collapsed(),

                Section::make('Border Radius')
                    ->description('Configure border radius presets with 4 corner values (top-left, top-right, bottom-right, bottom-left). Each preset can use different units (px, %, rem, em).')
                    ->schema([
                        Repeater::make('general.border_radius')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Preset Name')
                                    ->required()
                                    ->helperText('Name for this border radius preset (e.g., "Small", "Button", "Card")'),
                                Grid::make(5)
                                    ->schema([
                                        TextInput::make('top_left')
                                            ->label('Top Left')
                                            ->numeric()
                                            ->step(0.01)
                                            ->required()
                                            ->helperText('Top-left corner'),
                                        TextInput::make('top_right')
                                            ->label('Top Right')
                                            ->numeric()
                                            ->step(0.01)
                                            ->required()
                                            ->helperText('Top-right corner'),
                                        TextInput::make('bottom_right')
                                            ->label('Bottom Right')
                                            ->numeric()
                                            ->step(0.01)
                                            ->required()
                                            ->helperText('Bottom-right corner'),
                                        TextInput::make('bottom_left')
                                            ->label('Bottom Left')
                                            ->numeric()
                                            ->step(0.01)
                                            ->required()
                                            ->helperText('Bottom-left corner'),
                                        Select::make('unit')
                                            ->label('Unit')
                                            ->options([
                                                'px' => 'px',
                                                '%' => '%',
                                                'rem' => 'rem',
                                                'em' => 'em',
                                            ])
                                            ->required()
                                            ->default('rem'),
                                    ]),
                            ])
                            ->columns(1)
                            ->defaultItems(5)
                            ->itemLabel(fn (array $state): ?string => ($state['name'] ?? 'New Preset').' ('.($state['top_left'] ?? '0').($state['unit'] ?? 'rem').' / '.($state['top_right'] ?? '0').($state['unit'] ?? 'rem').' / '.($state['bottom_right'] ?? '0').($state['unit'] ?? 'rem').' / '.($state['bottom_left'] ?? '0').($state['unit'] ?? 'rem').')')
                            ->reorderable()
                            ->deletable()
                            ->addActionLabel('Add Border Radius Preset'),
                    ])
                    ->visible(fn () => $this->activeTab === 'general')
                    ->collapsible()
                    ->collapsed(),

                Section::make('Spacing Scale')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('general.spacing_xs')
                                    ->label('Extra Small')
                                    ->suffix('rem')
                                    ->default(fn () => $this->getVariableValue('general.spacing.xs', '0.25rem')),
                                TextInput::make('general.spacing_sm')
                                    ->label('Small')
                                    ->suffix('rem')
                                    ->default(fn () => $this->getVariableValue('general.spacing.sm', '0.5rem')),
                                TextInput::make('general.spacing_md')
                                    ->label('Medium')
                                    ->suffix('rem')
                                    ->default(fn () => $this->getVariableValue('general.spacing.md', '1rem')),
                                TextInput::make('general.spacing_lg')
                                    ->label('Large')
                                    ->suffix('rem')
                                    ->default(fn () => $this->getVariableValue('general.spacing.lg', '1.5rem')),
                                TextInput::make('general.spacing_xl')
                                    ->label('Extra Large')
                                    ->suffix('rem')
                                    ->default(fn () => $this->getVariableValue('general.spacing.xl', '2rem')),
                                TextInput::make('general.spacing_2xl')
                                    ->label('2X Large')
                                    ->suffix('rem')
                                    ->default(fn () => $this->getVariableValue('general.spacing.2xl', '3rem')),
                            ]),
                    ])
                    ->visible(fn () => $this->activeTab === 'general')
                    ->collapsible()
                    ->collapsed(),

                // Typography tab form - EN
                Section::make('Font Families (EN)')
                    ->description('Select fonts from Google Fonts or enter a custom font name. Browse available fonts at Google Fonts.')
                    ->schema([
                        Grid::make(2)
                            ->schema($this->createFieldsFromConfig('typography_en_fonts')),
                    ])
                    ->visible(fn () => $this->activeTab === 'typography' && $this->typographyLang === 'en')
                    ->collapsible(),

                Section::make('Font Sizes (EN)')
                    ->schema([
                        Grid::make(3)
                            ->schema($this->createFieldsFromConfig('typography_en_sizes')),
                    ])
                    ->visible(fn () => $this->activeTab === 'typography' && $this->typographyLang === 'en')
                    ->collapsible()
                    ->collapsed(),

                Section::make('Line Heights (EN)')
                    ->schema([
                        Grid::make(3)
                            ->schema($this->createFieldsFromConfig('typography_en_line_heights')),
                    ])
                    ->visible(fn () => $this->activeTab === 'typography' && $this->typographyLang === 'en')
                    ->collapsible()
                    ->collapsed(),

                // Typography tab form - GR
                Section::make('Font Families (GR)')
                    ->description('Select fonts from Google Fonts or enter a custom font name. Browse available fonts at Google Fonts.')
                    ->schema([
                        Grid::make(2)
                            ->schema($this->createFieldsFromConfig('typography_gr_fonts')),
                    ])
                    ->visible(fn () => $this->activeTab === 'typography' && $this->typographyLang === 'gr')
                    ->collapsible(),

                Section::make('Font Sizes (GR)')
                    ->schema([
                        Grid::make(3)
                            ->schema($this->createFieldsFromConfig('typography_gr_sizes')),
                    ])
                    ->visible(fn () => $this->activeTab === 'typography' && $this->typographyLang === 'gr')
                    ->collapsible()
                    ->collapsed(),

                Section::make('Line Heights (GR)')
                    ->schema([
                        Grid::make(3)
                            ->schema($this->createFieldsFromConfig('typography_gr_line_heights')),
                    ])
                    ->visible(fn () => $this->activeTab === 'typography' && $this->typographyLang === 'gr')
                    ->collapsible()
                    ->collapsed(),

                // Legacy tab form
                Section::make('Legacy Variables')
                    ->schema([
                        ...$this->createFieldsFromConfig('legacy'),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('legacy.spacing_small_value')
                                    ->label('Small Spacing')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(fn () => $this->parseSpacingValue($this->getVariableValue('legacy.spacing.small', '0.5rem'))['value']),
                                Select::make('legacy.spacing_small_unit')
                                    ->label('Unit')
                                    ->options([
                                        'px' => 'px',
                                        'rem' => 'rem',
                                        'em' => 'em',
                                        '%' => '%',
                                        'vh' => 'vh',
                                        'vw' => 'vw',
                                        'pt' => 'pt',
                                        'cm' => 'cm',
                                        'mm' => 'mm',
                                    ])
                                    ->default(fn () => $this->parseSpacingValue($this->getVariableValue('legacy.spacing.small', '0.5rem'))['unit'])
                                    ->required(),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('legacy.spacing_medium_value')
                                    ->label('Medium Spacing')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(fn () => $this->parseSpacingValue($this->getVariableValue('legacy.spacing.medium', '1rem'))['value']),
                                Select::make('legacy.spacing_medium_unit')
                                    ->label('Unit')
                                    ->options([
                                        'px' => 'px',
                                        'rem' => 'rem',
                                        'em' => 'em',
                                        '%' => '%',
                                        'vh' => 'vh',
                                        'vw' => 'vw',
                                        'pt' => 'pt',
                                        'cm' => 'cm',
                                        'mm' => 'mm',
                                    ])
                                    ->default(fn () => $this->parseSpacingValue($this->getVariableValue('legacy.spacing.medium', '1rem'))['unit'])
                                    ->required(),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('legacy.spacing_large_value')
                                    ->label('Large Spacing')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(fn () => $this->parseSpacingValue($this->getVariableValue('legacy.spacing.large', '2rem'))['value']),
                                Select::make('legacy.spacing_large_unit')
                                    ->label('Unit')
                                    ->options([
                                        'px' => 'px',
                                        'rem' => 'rem',
                                        'em' => 'em',
                                        '%' => '%',
                                        'vh' => 'vh',
                                        'vw' => 'vw',
                                        'pt' => 'pt',
                                        'cm' => 'cm',
                                        'mm' => 'mm',
                                    ])
                                    ->default(fn () => $this->parseSpacingValue($this->getVariableValue('legacy.spacing.large', '2rem'))['unit'])
                                    ->required(),
                            ]),
                    ])
                    ->visible(fn () => $this->activeTab === 'legacy')
                    ->collapsible(),

                // S.I. tab form
                Section::make('Length Classes')
                    ->schema([
                        Repeater::make('si.length_classes')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Name')
                                    ->required(),
                                TextInput::make('unit')
                                    ->label('Unit')
                                    ->required()
                                    ->maxLength(10),
                                TextInput::make('value')
                                    ->label('Value')
                                    ->numeric()
                                    ->step(0.000001)
                                    ->required(),
                            ])
                            ->columns(3)
                            ->defaultItems(3)
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                    ])
                    ->visible(fn () => $this->activeTab === 'si')
                    ->collapsible(),

                Section::make('Weight Classes')
                    ->schema([
                        Repeater::make('si.weight_classes')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Name')
                                    ->required(),
                                TextInput::make('unit')
                                    ->label('Unit')
                                    ->required()
                                    ->maxLength(10),
                                TextInput::make('value')
                                    ->label('Value')
                                    ->numeric()
                                    ->step(0.000001)
                                    ->required(),
                            ])
                            ->columns(3)
                            ->defaultItems(3)
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                    ])
                    ->visible(fn () => $this->activeTab === 'si')
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

        // Save variables based on active tab
        $variablesToSave = match ($this->activeTab) {
            'general' => array_merge(
                // Breakpoints
                $this->generateSaveVariablesFromConfig('breakpoints', $data, 'general'),
                // Colors
                $this->generateSaveVariablesFromConfig('colors', $data, 'general'),
                // Spacing
                $this->generateSaveVariablesFromConfig('spacing', $data, 'general'),
                // JSON arrays (Repeaters)
                array_filter([
                    ['key' => 'general.color_styles', 'value' => $data['general']['color_styles'] ?? null, 'type' => 'json'],
                    ['key' => 'general.shadow_presets', 'value' => $data['general']['shadow_presets'] ?? null, 'type' => 'json'],
                    ['key' => 'general.border_radius', 'value' => $data['general']['border_radius'] ?? null, 'type' => 'json'],
                ], fn ($item) => $item['value'] !== null)
            ),
            'typography' => match ($this->typographyLang) {
                'en' => array_merge(
                    $this->generateSaveVariablesFromConfig('typography_en_fonts', $data, 'typography_en'),
                    $this->generateSaveVariablesFromConfig('typography_en_sizes', $data, 'typography_en'),
                    $this->generateSaveVariablesFromConfig('typography_en_line_heights', $data, 'typography_en')
                ),
                'gr' => array_merge(
                    $this->generateSaveVariablesFromConfig('typography_gr_fonts', $data, 'typography_gr'),
                    $this->generateSaveVariablesFromConfig('typography_gr_sizes', $data, 'typography_gr'),
                    $this->generateSaveVariablesFromConfig('typography_gr_line_heights', $data, 'typography_gr')
                ),
                default => [],
            },
            'legacy' => array_merge(
                $this->generateSaveVariablesFromConfig('legacy', $data, 'legacy'),
                // Special handling for legacy spacing (value + unit combination)
                array_filter([
                    [
                        'key' => 'legacy.spacing.small',
                        'value' => isset($data['legacy']['spacing_small_value'])
                            ? ($data['legacy']['spacing_small_value'].($data['legacy']['spacing_small_unit'] ?? 'rem'))
                            : null,
                        'type' => 'string',
                    ],
                    [
                        'key' => 'legacy.spacing.medium',
                        'value' => isset($data['legacy']['spacing_medium_value'])
                            ? ($data['legacy']['spacing_medium_value'].($data['legacy']['spacing_medium_unit'] ?? 'rem'))
                            : null,
                        'type' => 'string',
                    ],
                    [
                        'key' => 'legacy.spacing.large',
                        'value' => isset($data['legacy']['spacing_large_value'])
                            ? ($data['legacy']['spacing_large_value'].($data['legacy']['spacing_large_unit'] ?? 'rem'))
                            : null,
                        'type' => 'string',
                    ],
                ], fn ($item) => $item['value'] !== null)
            ),
            'si' => [
                ['key' => 'si.length_classes', 'value' => $data['si']['length_classes'] ?? [], 'type' => 'json'],
                ['key' => 'si.weight_classes', 'value' => $data['si']['weight_classes'] ?? [], 'type' => 'json'],
            ],
            default => [],
        };

        foreach ($variablesToSave as $varData) {
            // Skip if value is null (not provided)
            if ($varData['value'] === null) {
                continue;
            }

            $variable = Variable::firstOrNew([
                'business_id' => $business->id,
                'key' => $varData['key'],
            ]);

            $variable->type = $varData['type'];

            if ($varData['type'] === 'json') {
                $variable->value = is_array($varData['value']) ? json_encode($varData['value']) : $varData['value'];
            } elseif ($varData['type'] === 'number') {
                $variable->value = (string) $varData['value'];
            } else {
                $variable->value = (string) $varData['value'];
            }

            $variable->save();
        }

        // Clear cache for all saved variables (like Journal theme approach)
        foreach ($variablesToSave as $varData) {
            $cacheKey = "cms_variable:{$business->id}:{$varData['key']}";
            Cache::forget($cacheKey);
        }

        // Clear typography fonts cache if typography variables were saved
        if ($this->activeTab === 'typography' && $business) {
            $typographyService = app(\App\Domain\Variables\Services\GetTypographyFontsService::class);
            $typographyService->clearCache($business);

            // Clear VariableHelper cache for typography keys (without tags)
            $langPrefix = $this->typographyLang;
            $typographyKeys = [
                "typography.{$langPrefix}.font.primary",
                "typography.{$langPrefix}.font.secondary",
                "typography.{$langPrefix}.font.heading",
                "typography.{$langPrefix}.font.body",
                "typography.{$langPrefix}.size.xs",
                "typography.{$langPrefix}.size.sm",
                "typography.{$langPrefix}.size.base",
                "typography.{$langPrefix}.size.lg",
                "typography.{$langPrefix}.size.xl",
                "typography.{$langPrefix}.size.2xl",
                "typography.{$langPrefix}.size.3xl",
                "typography.{$langPrefix}.size.4xl",
                "typography.{$langPrefix}.line_height.tight",
                "typography.{$langPrefix}.line_height.normal",
                "typography.{$langPrefix}.line_height.relaxed",
            ];

            foreach ($typographyKeys as $key) {
                Cache::forget("variable:{$business->id}:{$key}");
            }
        }

        Notification::make()
            ->success()
            ->title('Variables saved successfully')
            ->send();
    }

    public static function getSlug(?\Filament\Panel $panel = null): string
    {
        return 'cms-variables';
    }

    public function getTitle(): string
    {
        return 'Variables';
    }

    /**
     * Get header actions (refresh button for typography tab)
     * Note: Header actions are cached by Filament, so we remove the button entirely
     * and use the inline button in the blade template instead
     */
    protected function getHeaderActions(): array
    {
        // Return empty array - we use inline button in blade template instead
        // This prevents the button from appearing in header when switching tabs
        return [];
    }

    /**
     * Build Google Fonts API URL with optional API key
     */
    protected function buildGoogleFontsApiUrl(array $params = []): array
    {
        $url = 'https://www.googleapis.com/webfonts/v1/webfonts';
        $apiKey = config('services.google_fonts.api_key');

        if ($apiKey) {
            $params['key'] = $apiKey;
        }

        return [$url, $params];
    }

    /**
     * Get favorite fonts from database
     * Returns empty array if not found - values should be managed via variables table
     */
    protected function getFavoriteFonts(): array
    {
        $business = Business::active()->first();
        if (! $business) {
            return [];
        }

        $favoriteFonts = $this->getVariableValue('typography.favorite_fonts', []);

        // If it's a string (JSON), decode it
        if (is_string($favoriteFonts)) {
            $decoded = json_decode($favoriteFonts, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        // If it's already an array, return it
        if (is_array($favoriteFonts)) {
            return $favoriteFonts;
        }

        return [];
    }

    /**
     * Organize fonts array with favorites first, then remaining alphabetically
     */
    protected function organizeFontsWithFavorites(array $fonts): array
    {
        $favorites = $this->getFavoriteFonts();
        $organizedFonts = [];
        $remainingFonts = [];

        // Separate favorites from remaining fonts
        foreach ($fonts as $fontName => $fontValue) {
            if (in_array($fontName, $favorites)) {
                $organizedFonts[$fontName] = $fontValue;
            } else {
                $remainingFonts[$fontName] = $fontValue;
            }
        }

        // Sort favorites in the order they appear in the list
        $sortedFavorites = [];
        foreach ($favorites as $favorite) {
            if (isset($organizedFonts[$favorite])) {
                $sortedFavorites[$favorite] = $organizedFonts[$favorite];
            }
        }

        // Combine: favorites first, then remaining fonts alphabetically
        return array_merge($sortedFavorites, $remainingFonts);
    }

    /**
     * Get all Google Fonts options from API (with cache)
     * Filters only fonts with latin or greek subsets
     * Falls back to cached result if API is unavailable (no hardcoded fonts)
     * Returns fonts with favorites first
     */
    protected function getGoogleFontsOptions(): array
    {
        // Cache key for successful API response
        $cacheKey = 'google_fonts_list_filtered';
        // Cache key for fallback (last successful result)
        $fallbackCacheKey = 'google_fonts_list_fallback';

        // First, try to get from cache (faster)
        // Note: We still need to reorganize with favorites first even from cache
        $cachedFonts = Cache::get($cacheKey);
        if ($cachedFonts && is_array($cachedFonts) && ! empty($cachedFonts)) {
            // Reorganize cached fonts with favorites first
            return $this->organizeFontsWithFavorites($cachedFonts);
        }

        try {
            // Build API URL with optional API key
            [$url, $params] = $this->buildGoogleFontsApiUrl(['sort' => 'popularity']);

            // Try to fetch from Google Fonts API (sorted by popularity)
            $response = Http::timeout(5)->get($url, $params);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['items']) && is_array($data['items'])) {
                    $fonts = [];

                    foreach ($data['items'] as $font) {
                        if (! isset($font['family']) || ! isset($font['subsets'])) {
                            continue;
                        }

                        // Filter: only fonts with latin or greek subsets
                        $subsets = is_array($font['subsets']) ? $font['subsets'] : [];
                        if (in_array('latin', $subsets) || in_array('greek', $subsets)) {
                            $fonts[$font['family']] = $font['family'];
                        }
                    }

                    // Sort alphabetically
                    ksort($fonts);

                    // If we got fonts, organize with favorites first
                    if (! empty($fonts)) {
                        $finalFonts = $this->organizeFontsWithFavorites($fonts);

                        // Cache successful result for 24 hours
                        Cache::put($cacheKey, $finalFonts, 86400);
                        // Also save as fallback (longer cache - 7 days)
                        Cache::put($fallbackCacheKey, $finalFonts, 604800);

                        return $finalFonts;
                    }
                }
            }
        } catch (\Exception $e) {
            // If API fails, try to use cached fallback
            Log::warning('Google Fonts API request failed: '.$e->getMessage());
        }

        // Fallback: Try to get last cached successful result (longer cache)
        $fallbackFonts = Cache::get($fallbackCacheKey);
        if ($fallbackFonts && is_array($fallbackFonts) && ! empty($fallbackFonts)) {
            // Reorganize fallback fonts with favorites first
            return $this->organizeFontsWithFavorites($fallbackFonts);
        }

        // If no API response and no cache, return empty array (no hardcoded fonts)
        // The dropdown will be empty until API is available or cache is populated
        return [];
    }

    /**
     * Search Google Fonts (returns matching fonts or allows custom entry)
     */
    protected function searchGoogleFonts(string $search): array
    {
        $allFonts = $this->getGoogleFontsOptions();
        $searchLower = strtolower(trim($search));

        if (empty($searchLower)) {
            return $allFonts;
        }

        $filtered = array_filter($allFonts, function ($font) use ($searchLower) {
            return str_contains(strtolower($font), $searchLower);
        });

        // If no match found, allow custom entry by adding the search term
        if (empty($filtered) && ! empty($searchLower)) {
            // Capitalize first letter of each word for custom font
            $customFont = ucwords($searchLower);

            return [$customFont => $customFont.' (custom)'];
        }

        // Maintain favorites-first order in search results
        return $this->organizeFontsWithFavorites($filtered);
    }

    /**
     * Parse spacing value to extract numeric value and unit
     */
    protected function parseSpacingValue(string $value): array
    {
        // Match number and unit (e.g., "0.5rem", "10px", "1em")
        if (preg_match('/^([\d.]+)(px|rem|em|%|vh|vw|pt|cm|mm)$/i', trim($value), $matches)) {
            return [
                'value' => (float) $matches[1],
                'unit' => $matches[2],
            ];
        }

        // Default fallback
        return [
            'value' => 0.5,
            'unit' => 'rem',
        ];
    }

    /**
     * Generate save variables array from field configs
     */
    protected function generateSaveVariablesFromConfig(string $configKey, array $data, string $dataPath = ''): array
    {
        $configs = $this->getFieldConfigs();
        $variables = [];

        if (! isset($configs[$configKey])) {
            return [];
        }

        foreach ($configs[$configKey] as $config) {
            // Field path is already in format like 'general.breakpoint_sm' or 'typography_en.font_primary'
            // So we use it directly with data_get
            $value = data_get($data, $config['field']);

            if ($value !== null && $value !== '') {
                $variables[] = [
                    'key' => $config['key'],
                    'value' => $value,
                    'type' => $this->getVariableTypeFromConfig($config),
                ];
            }
        }

        return $variables;
    }

    /**
     * Get variable type from config (defaults to string)
     */
    protected function getVariableTypeFromConfig(array $config): string
    {
        if (isset($config['numeric']) && $config['numeric']) {
            return 'number';
        }

        if (str_contains($config['key'], 'color')) {
            return 'string';
        }

        return 'string';
    }

    /**
     * Get field configuration for dynamic field generation
     */
    protected function getFieldConfigs(): array
    {
        return [
            'breakpoints' => [
                ['key' => 'general.breakpoint.sm', 'field' => 'general.breakpoint_sm', 'label' => 'Small (sm)', 'default' => '640px', 'suffix' => 'px', 'placeholder' => 'e.g., 640px'],
                ['key' => 'general.breakpoint.md', 'field' => 'general.breakpoint_md', 'label' => 'Medium (md)', 'default' => '768px', 'suffix' => 'px', 'placeholder' => 'e.g., 768px'],
                ['key' => 'general.breakpoint.lg', 'field' => 'general.breakpoint_lg', 'label' => 'Large (lg)', 'default' => '1024px', 'suffix' => 'px', 'placeholder' => 'e.g., 1024px'],
                ['key' => 'general.breakpoint.xl', 'field' => 'general.breakpoint_xl', 'label' => 'Extra Large (xl)', 'default' => '1280px', 'suffix' => 'px', 'placeholder' => 'e.g., 1280px'],
                ['key' => 'general.breakpoint.2xl', 'field' => 'general.breakpoint_2xl', 'label' => '2X Large (2xl)', 'default' => '1536px', 'suffix' => 'px', 'placeholder' => 'e.g., 1536px'],
            ],
            'colors' => [
                ['key' => 'general.color.primary', 'field' => 'general.color_primary', 'label' => 'Primary Color', 'default' => '#3b82f6'],
                ['key' => 'general.color.secondary', 'field' => 'general.color_secondary', 'label' => 'Secondary Color', 'default' => '#8b5cf6'],
                ['key' => 'general.color.accent', 'field' => 'general.color_accent', 'label' => 'Accent Color', 'default' => '#10b981'],
                ['key' => 'general.color.success', 'field' => 'general.color_success', 'label' => 'Success Color', 'default' => '#22c55e'],
                ['key' => 'general.color.warning', 'field' => 'general.color_warning', 'label' => 'Warning Color', 'default' => '#f59e0b'],
                ['key' => 'general.color.danger', 'field' => 'general.color_danger', 'label' => 'Danger Color', 'default' => '#ef4444'],
                ['key' => 'general.color.info', 'field' => 'general.color_info', 'label' => 'Info Color', 'default' => '#06b6d4'],
                ['key' => 'general.color.background', 'field' => 'general.color_background', 'label' => 'Background Color', 'default' => '#ffffff'],
                ['key' => 'general.color.text', 'field' => 'general.color_text', 'label' => 'Text Color', 'default' => '#1f2937'],
            ],
            'spacing' => [
                ['key' => 'general.spacing.xs', 'field' => 'general.spacing_xs', 'label' => 'Extra Small', 'default' => '0.25rem', 'suffix' => 'rem'],
                ['key' => 'general.spacing.sm', 'field' => 'general.spacing_sm', 'label' => 'Small', 'default' => '0.5rem', 'suffix' => 'rem'],
                ['key' => 'general.spacing.md', 'field' => 'general.spacing_md', 'label' => 'Medium', 'default' => '1rem', 'suffix' => 'rem'],
                ['key' => 'general.spacing.lg', 'field' => 'general.spacing_lg', 'label' => 'Large', 'default' => '1.5rem', 'suffix' => 'rem'],
                ['key' => 'general.spacing.xl', 'field' => 'general.spacing_xl', 'label' => 'Extra Large', 'default' => '2rem', 'suffix' => 'rem'],
                ['key' => 'general.spacing.2xl', 'field' => 'general.spacing_2xl', 'label' => '2X Large', 'default' => '3rem', 'suffix' => 'rem'],
            ],
            'typography_en_fonts' => [
                ['key' => 'typography.en.font.primary', 'field' => 'typography_en.font_primary', 'label' => 'Primary Font', 'default' => 'Inter', 'helper' => 'Primary font used for main content and general text throughout the website. Favorites appear first in the dropdown.'],
                ['key' => 'typography.en.font.secondary', 'field' => 'typography_en.font_secondary', 'label' => 'Secondary Font', 'default' => 'Inter', 'helper' => 'Secondary font used for accents, quotes, or alternative text elements. Favorites appear first in the dropdown.'],
                ['key' => 'typography.en.font.heading', 'field' => 'typography_en.font_heading', 'label' => 'Heading Font', 'default' => 'Inter', 'helper' => 'Font used for all headings (H1, H2, H3, etc.) and titles. Typically a bold or distinctive font. Favorites appear first in the dropdown.'],
                ['key' => 'typography.en.font.body', 'field' => 'typography_en.font_body', 'label' => 'Body Font', 'default' => 'Inter', 'helper' => 'Font used for body text, paragraphs, and main content. Should be highly readable. Favorites appear first in the dropdown.'],
            ],
            'typography_en_sizes' => [
                ['key' => 'typography.en.size.xs', 'field' => 'typography_en.font_size_xs', 'label' => 'Extra Small', 'default' => '0.75rem', 'suffix' => 'rem'],
                ['key' => 'typography.en.size.sm', 'field' => 'typography_en.font_size_sm', 'label' => 'Small', 'default' => '0.875rem', 'suffix' => 'rem'],
                ['key' => 'typography.en.size.base', 'field' => 'typography_en.font_size_base', 'label' => 'Base', 'default' => '1rem', 'suffix' => 'rem'],
                ['key' => 'typography.en.size.lg', 'field' => 'typography_en.font_size_lg', 'label' => 'Large', 'default' => '1.125rem', 'suffix' => 'rem'],
                ['key' => 'typography.en.size.xl', 'field' => 'typography_en.font_size_xl', 'label' => 'Extra Large', 'default' => '1.25rem', 'suffix' => 'rem'],
                ['key' => 'typography.en.size.2xl', 'field' => 'typography_en.font_size_2xl', 'label' => '2X Large', 'default' => '1.5rem', 'suffix' => 'rem'],
                ['key' => 'typography.en.size.3xl', 'field' => 'typography_en.font_size_3xl', 'label' => '3X Large', 'default' => '1.875rem', 'suffix' => 'rem'],
                ['key' => 'typography.en.size.4xl', 'field' => 'typography_en.font_size_4xl', 'label' => '4X Large', 'default' => '2.25rem', 'suffix' => 'rem'],
            ],
            'typography_en_line_heights' => [
                ['key' => 'typography.en.line_height.tight', 'field' => 'typography_en.line_height_tight', 'label' => 'Tight', 'default' => '1.25', 'numeric' => true, 'step' => 0.01],
                ['key' => 'typography.en.line_height.normal', 'field' => 'typography_en.line_height_normal', 'label' => 'Normal', 'default' => '1.5', 'numeric' => true, 'step' => 0.01],
                ['key' => 'typography.en.line_height.relaxed', 'field' => 'typography_en.line_height_relaxed', 'label' => 'Relaxed', 'default' => '1.75', 'numeric' => true, 'step' => 0.01],
            ],
            'typography_gr_fonts' => [
                ['key' => 'typography.gr.font.primary', 'field' => 'typography_gr.font_primary', 'label' => 'Primary Font', 'default' => 'Inter', 'helper' => 'Κύρια γραμματοσειρά που χρησιμοποιείται για το κύριο περιεχόμενο και γενικό κείμενο σε όλο τον ιστότοπο. Τα αγαπημένα εμφανίζονται πρώτα στο dropdown.'],
                ['key' => 'typography.gr.font.secondary', 'field' => 'typography_gr.font_secondary', 'label' => 'Secondary Font', 'default' => 'Inter', 'helper' => 'Δευτερεύουσα γραμματοσειρά που χρησιμοποιείται για επισημάνσεις, παραθέσεις ή εναλλακτικά στοιχεία κειμένου. Τα αγαπημένα εμφανίζονται πρώτα στο dropdown.'],
                ['key' => 'typography.gr.font.heading', 'field' => 'typography_gr.font_heading', 'label' => 'Heading Font', 'default' => 'Inter', 'helper' => 'Γραμματοσειρά που χρησιμοποιείται για όλους τους τίτλους (H1, H2, H3, κλπ.) και επικεφαλίδες. Συνήθως μια έντονη ή διακριτική γραμματοσειρά. Τα αγαπημένα εμφανίζονται πρώτα στο dropdown.'],
                ['key' => 'typography.gr.font.body', 'field' => 'typography_gr.font_body', 'label' => 'Body Font', 'default' => 'Inter', 'helper' => 'Γραμματοσειρά που χρησιμοποιείται για το σώμα κειμένου, παραγράφους και κύριο περιεχόμενο. Πρέπει να είναι πολύ ευανάγνωστη. Τα αγαπημένα εμφανίζονται πρώτα στο dropdown.'],
            ],
            'typography_gr_sizes' => [
                ['key' => 'typography.gr.size.xs', 'field' => 'typography_gr.font_size_xs', 'label' => 'Extra Small', 'default' => '0.75rem', 'suffix' => 'rem'],
                ['key' => 'typography.gr.size.sm', 'field' => 'typography_gr.font_size_sm', 'label' => 'Small', 'default' => '0.875rem', 'suffix' => 'rem'],
                ['key' => 'typography.gr.size.base', 'field' => 'typography_gr.font_size_base', 'label' => 'Base', 'default' => '1rem', 'suffix' => 'rem'],
                ['key' => 'typography.gr.size.lg', 'field' => 'typography_gr.font_size_lg', 'label' => 'Large', 'default' => '1.125rem', 'suffix' => 'rem'],
                ['key' => 'typography.gr.size.xl', 'field' => 'typography_gr.font_size_xl', 'label' => 'Extra Large', 'default' => '1.25rem', 'suffix' => 'rem'],
                ['key' => 'typography.gr.size.2xl', 'field' => 'typography_gr.font_size_2xl', 'label' => '2X Large', 'default' => '1.5rem', 'suffix' => 'rem'],
                ['key' => 'typography.gr.size.3xl', 'field' => 'typography_gr.font_size_3xl', 'label' => '3X Large', 'default' => '1.875rem', 'suffix' => 'rem'],
                ['key' => 'typography.gr.size.4xl', 'field' => 'typography_gr.font_size_4xl', 'label' => '4X Large', 'default' => '2.25rem', 'suffix' => 'rem'],
            ],
            'typography_gr_line_heights' => [
                ['key' => 'typography.gr.line_height.tight', 'field' => 'typography_gr.line_height_tight', 'label' => 'Tight', 'default' => '1.25', 'numeric' => true, 'step' => 0.01],
                ['key' => 'typography.gr.line_height.normal', 'field' => 'typography_gr.line_height_normal', 'label' => 'Normal', 'default' => '1.5', 'numeric' => true, 'step' => 0.01],
                ['key' => 'typography.gr.line_height.relaxed', 'field' => 'typography_gr.line_height_relaxed', 'label' => 'Relaxed', 'default' => '1.75', 'numeric' => true, 'step' => 0.01],
            ],
            'legacy' => [
                ['key' => 'legacy.items_per_row', 'field' => 'legacy.items_per_row', 'label' => 'Items per Row', 'default' => 4, 'numeric' => true, 'helper' => 'Default items per row for grid layouts'],
            ],
        ];
    }

    /**
     * Create TextInput field from configuration
     */
    protected function createTextInputField(array $config): TextInput
    {
        $field = TextInput::make($config['field'])
            ->label($config['label'])
            ->default(fn () => $this->getVariableValue($config['key'], $config['default']));

        if (isset($config['suffix'])) {
            $field->suffix($config['suffix']);
        }

        if (isset($config['placeholder'])) {
            $field->placeholder($config['placeholder']);
        }

        if (isset($config['numeric']) && $config['numeric']) {
            $field->numeric();
            if (isset($config['step'])) {
                $field->step($config['step']);
            }
        }

        if (isset($config['helper'])) {
            $field->helperText($config['helper']);
        }

        return $field;
    }

    /**
     * Create ColorPicker field from configuration
     */
    protected function createColorPickerField(array $config): ColorPicker
    {
        return ColorPicker::make($config['field'])
            ->label($config['label'])
            ->default(fn () => $this->getVariableValue($config['key'], $config['default']));
    }

    /**
     * Create Select field for fonts from configuration
     */
    protected function createFontSelectField(array $config): Select
    {
        $field = Select::make($config['field'])
            ->label($config['label'])
            ->options($this->getGoogleFontsOptions())
            ->searchable()
            ->preload()
            ->getSearchResultsUsing(fn (string $search) => $this->searchGoogleFonts($search))
            ->default(fn () => $this->getVariableValue($config['key'], $config['default']));

        if (isset($config['helper'])) {
            $field->helperText($config['helper']);
        }

        return $field;
    }

    /**
     * Create fields from configuration array
     */
    protected function createFieldsFromConfig(string $configKey, int $columns = 2): array
    {
        $configs = $this->getFieldConfigs();
        $fields = [];

        if (! isset($configs[$configKey])) {
            return [];
        }

        foreach ($configs[$configKey] as $config) {
            // Determine field type based on config or key
            if (str_contains($config['key'], 'color')) {
                $fields[] = $this->createColorPickerField($config);
            } elseif (str_contains($config['key'], 'font')) {
                $fields[] = $this->createFontSelectField($config);
            } else {
                $fields[] = $this->createTextInputField($config);
            }
        }

        return $fields;
    }
}
