<?php

namespace App\Filament\Pages\CMS;

use App\Domain\Businesses\Models\Business;
use App\Domain\Variables\Models\Variable;
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

class Variables extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.cms.variables';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-variable';

    protected static ?string $navigationLabel = 'CMS Variables';

    public string $activeTab = 'general';

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

        // Load existing variables for each tab
        $this->loadVariables($business);
    }

    protected function loadVariables(Business $business): void
    {
        $variables = Variable::forBusiness($business->id)->get()->keyBy('key');

        // Helper to get variable value or default
        $getVar = function (string $key, mixed $default = null) use ($variables) {
            $var = $variables->get($key);
            if (! $var) {
                return $default;
            }

            // For JSON type, decode it
            if ($var->type === 'json') {
                return json_decode($var->value, true) ?? $default;
            }

            // For number type, return as number
            if ($var->type === 'number') {
                return is_numeric($var->value) ? (str_contains($var->value, '.') ? (float) $var->value : (int) $var->value) : $default;
            }

            // For boolean type
            if ($var->type === 'boolean') {
                return filter_var($var->value, FILTER_VALIDATE_BOOLEAN);
            }

            return $var->value ?? $default;
        };

        $this->form->fill([
            // General tab - Breakpoints
            'general' => [
                'breakpoint_sm' => $getVar('general.breakpoint.sm', '640px'),
                'breakpoint_md' => $getVar('general.breakpoint.md', '768px'),
                'breakpoint_lg' => $getVar('general.breakpoint.lg', '1024px'),
                'breakpoint_xl' => $getVar('general.breakpoint.xl', '1280px'),
                'breakpoint_2xl' => $getVar('general.breakpoint.2xl', '1536px'),
                // Color Tokens
                'color_primary' => $getVar('general.color.primary', '#3b82f6'),
                'color_secondary' => $getVar('general.color.secondary', '#8b5cf6'),
                'color_accent' => $getVar('general.color.accent', '#10b981'),
                'color_success' => $getVar('general.color.success', '#22c55e'),
                'color_warning' => $getVar('general.color.warning', '#f59e0b'),
                'color_danger' => $getVar('general.color.danger', '#ef4444'),
                'color_info' => $getVar('general.color.info', '#06b6d4'),
                'color_background' => $getVar('general.color.background', '#ffffff'),
                'color_text' => $getVar('general.color.text', '#1f2937'),
                // Shadow
                'shadow_sm' => $getVar('general.shadow.sm', '0 1px 2px 0 rgb(0 0 0 / 0.05)'),
                'shadow_md' => $getVar('general.shadow.md', '0 4px 6px -1px rgb(0 0 0 / 0.1)'),
                'shadow_lg' => $getVar('general.shadow.lg', '0 10px 15px -3px rgb(0 0 0 / 0.1)'),
                'shadow_xl' => $getVar('general.shadow.xl', '0 20px 25px -5px rgb(0 0 0 / 0.1)'),
                // Border Radius
                'radius_sm' => $getVar('general.radius.sm', '0.125rem'),
                'radius_md' => $getVar('general.radius.md', '0.375rem'),
                'radius_lg' => $getVar('general.radius.lg', '0.5rem'),
                'radius_xl' => $getVar('general.radius.xl', '0.75rem'),
                'radius_full' => $getVar('general.radius.full', '9999px'),
                // Spacing
                'spacing_xs' => $getVar('general.spacing.xs', '0.25rem'),
                'spacing_sm' => $getVar('general.spacing.sm', '0.5rem'),
                'spacing_md' => $getVar('general.spacing.md', '1rem'),
                'spacing_lg' => $getVar('general.spacing.lg', '1.5rem'),
                'spacing_xl' => $getVar('general.spacing.xl', '2rem'),
                'spacing_2xl' => $getVar('general.spacing.2xl', '3rem'),
            ],
            // Typography tab
            'typography' => [
                'font_primary' => $getVar('typography.font.primary', 'Inter'),
                'font_secondary' => $getVar('typography.font.secondary', 'Inter'),
                'font_heading' => $getVar('typography.font.heading', 'Inter'),
                'font_body' => $getVar('typography.font.body', 'Inter'),
                'font_size_xs' => $getVar('typography.size.xs', '0.75rem'),
                'font_size_sm' => $getVar('typography.size.sm', '0.875rem'),
                'font_size_base' => $getVar('typography.size.base', '1rem'),
                'font_size_lg' => $getVar('typography.size.lg', '1.125rem'),
                'font_size_xl' => $getVar('typography.size.xl', '1.25rem'),
                'font_size_2xl' => $getVar('typography.size.2xl', '1.5rem'),
                'font_size_3xl' => $getVar('typography.size.3xl', '1.875rem'),
                'font_size_4xl' => $getVar('typography.size.4xl', '2.25rem'),
                'line_height_tight' => $getVar('typography.line_height.tight', '1.25'),
                'line_height_normal' => $getVar('typography.line_height.normal', '1.5'),
                'line_height_relaxed' => $getVar('typography.line_height.relaxed', '1.75'),
            ],
            // Legacy tab
            'legacy' => [
                'items_per_row' => $getVar('legacy.items_per_row', 4),
                'spacing_small_value' => $this->parseSpacingValue($getVar('legacy.spacing.small', '0.5rem'))['value'],
                'spacing_small_unit' => $this->parseSpacingValue($getVar('legacy.spacing.small', '0.5rem'))['unit'],
                'spacing_medium_value' => $this->parseSpacingValue($getVar('legacy.spacing.medium', '1rem'))['value'],
                'spacing_medium_unit' => $this->parseSpacingValue($getVar('legacy.spacing.medium', '1rem'))['unit'],
                'spacing_large_value' => $this->parseSpacingValue($getVar('legacy.spacing.large', '2rem'))['value'],
                'spacing_large_unit' => $this->parseSpacingValue($getVar('legacy.spacing.large', '2rem'))['unit'],
            ],
            // S.I. tab
            'si' => [
                'length_classes' => $getVar('si.length_classes', [
                    ['name' => 'Centimeter', 'unit' => 'cm', 'value' => 1.0],
                    ['name' => 'Millimeter', 'unit' => 'mm', 'value' => 0.1],
                    ['name' => 'Inch', 'unit' => 'in', 'value' => 0.393701],
                ]),
                'weight_classes' => $getVar('si.weight_classes', [
                    ['name' => 'Kilogram', 'unit' => 'kg', 'value' => 1.0],
                    ['name' => 'Gram', 'unit' => 'g', 'value' => 0.001],
                    ['name' => 'Pound', 'unit' => 'lb', 'value' => 0.453592],
                ]),
            ],
        ]);
    }

    public function updatedActiveTab(): void
    {
        // Reload form data when tab changes
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
                            ->schema([
                                TextInput::make('general.breakpoint_sm')
                                    ->label('Small (sm)')
                                    ->placeholder('e.g., 640px')
                                    ->suffix('px'),
                                TextInput::make('general.breakpoint_md')
                                    ->label('Medium (md)')
                                    ->placeholder('e.g., 768px')
                                    ->suffix('px'),
                                TextInput::make('general.breakpoint_lg')
                                    ->label('Large (lg)')
                                    ->placeholder('e.g., 1024px')
                                    ->suffix('px'),
                                TextInput::make('general.breakpoint_xl')
                                    ->label('Extra Large (xl)')
                                    ->placeholder('e.g., 1280px')
                                    ->suffix('px'),
                                TextInput::make('general.breakpoint_2xl')
                                    ->label('2X Large (2xl)')
                                    ->placeholder('e.g., 1536px')
                                    ->suffix('px'),
                            ]),
                    ])
                    ->visible(fn () => $this->activeTab === 'general')
                    ->collapsible()
                    ->collapsed(),

                Section::make('Color Tokens')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                ColorPicker::make('general.color_primary')
                                    ->label('Primary Color'),
                                ColorPicker::make('general.color_secondary')
                                    ->label('Secondary Color'),
                                ColorPicker::make('general.color_accent')
                                    ->label('Accent Color'),
                                ColorPicker::make('general.color_success')
                                    ->label('Success Color'),
                                ColorPicker::make('general.color_warning')
                                    ->label('Warning Color'),
                                ColorPicker::make('general.color_danger')
                                    ->label('Danger Color'),
                                ColorPicker::make('general.color_info')
                                    ->label('Info Color'),
                                ColorPicker::make('general.color_background')
                                    ->label('Background Color'),
                                ColorPicker::make('general.color_text')
                                    ->label('Text Color'),
                            ]),
                    ])
                    ->visible(fn () => $this->activeTab === 'general')
                    ->collapsible(),

                Section::make('Shadow Presets')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('general.shadow_sm')
                                    ->label('Small Shadow'),
                                TextInput::make('general.shadow_md')
                                    ->label('Medium Shadow'),
                                TextInput::make('general.shadow_lg')
                                    ->label('Large Shadow'),
                                TextInput::make('general.shadow_xl')
                                    ->label('Extra Large Shadow'),
                            ]),
                    ])
                    ->visible(fn () => $this->activeTab === 'general')
                    ->collapsible()
                    ->collapsed(),

                Section::make('Border Radius')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('general.radius_sm')
                                    ->label('Small')
                                    ->suffix('rem'),
                                TextInput::make('general.radius_md')
                                    ->label('Medium')
                                    ->suffix('rem'),
                                TextInput::make('general.radius_lg')
                                    ->label('Large')
                                    ->suffix('rem'),
                                TextInput::make('general.radius_xl')
                                    ->label('Extra Large')
                                    ->suffix('rem'),
                                TextInput::make('general.radius_full')
                                    ->label('Full')
                                    ->suffix('px'),
                            ]),
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
                                    ->suffix('rem'),
                                TextInput::make('general.spacing_sm')
                                    ->label('Small')
                                    ->suffix('rem'),
                                TextInput::make('general.spacing_md')
                                    ->label('Medium')
                                    ->suffix('rem'),
                                TextInput::make('general.spacing_lg')
                                    ->label('Large')
                                    ->suffix('rem'),
                                TextInput::make('general.spacing_xl')
                                    ->label('Extra Large')
                                    ->suffix('rem'),
                                TextInput::make('general.spacing_2xl')
                                    ->label('2X Large')
                                    ->suffix('rem'),
                            ]),
                    ])
                    ->visible(fn () => $this->activeTab === 'general')
                    ->collapsible()
                    ->collapsed(),

                // Typography tab form
                Section::make('Font Families')
                    ->description('Select fonts from Google Fonts or enter a custom font name. Browse available fonts at Google Fonts.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('typography.font_primary')
                                    ->label('Primary Font')
                                    ->options($this->getGoogleFontsOptions())
                                    ->searchable()
                                    ->getSearchResultsUsing(fn (string $search) => $this->searchGoogleFonts($search))
                                    ->helperText('Select from Google Fonts or type custom font name. Visit fonts.google.com to browse available fonts.'),
                                Select::make('typography.font_secondary')
                                    ->label('Secondary Font')
                                    ->options($this->getGoogleFontsOptions())
                                    ->searchable()
                                    ->getSearchResultsUsing(fn (string $search) => $this->searchGoogleFonts($search))
                                    ->helperText('Select from Google Fonts or type custom font name. Visit fonts.google.com to browse available fonts.'),
                                Select::make('typography.font_heading')
                                    ->label('Heading Font')
                                    ->options($this->getGoogleFontsOptions())
                                    ->searchable()
                                    ->getSearchResultsUsing(fn (string $search) => $this->searchGoogleFonts($search))
                                    ->helperText('Select from Google Fonts or type custom font name. Visit fonts.google.com to browse available fonts.'),
                                Select::make('typography.font_body')
                                    ->label('Body Font')
                                    ->options($this->getGoogleFontsOptions())
                                    ->searchable()
                                    ->getSearchResultsUsing(fn (string $search) => $this->searchGoogleFonts($search))
                                    ->helperText('Select from Google Fonts or type custom font name. Visit fonts.google.com to browse available fonts.'),
                            ]),
                    ])
                    ->visible(fn () => $this->activeTab === 'typography')
                    ->collapsible(),

                Section::make('Font Sizes')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('typography.font_size_xs')
                                    ->label('Extra Small')
                                    ->suffix('rem'),
                                TextInput::make('typography.font_size_sm')
                                    ->label('Small')
                                    ->suffix('rem'),
                                TextInput::make('typography.font_size_base')
                                    ->label('Base')
                                    ->suffix('rem'),
                                TextInput::make('typography.font_size_lg')
                                    ->label('Large')
                                    ->suffix('rem'),
                                TextInput::make('typography.font_size_xl')
                                    ->label('Extra Large')
                                    ->suffix('rem'),
                                TextInput::make('typography.font_size_2xl')
                                    ->label('2X Large')
                                    ->suffix('rem'),
                                TextInput::make('typography.font_size_3xl')
                                    ->label('3X Large')
                                    ->suffix('rem'),
                                TextInput::make('typography.font_size_4xl')
                                    ->label('4X Large')
                                    ->suffix('rem'),
                            ]),
                    ])
                    ->visible(fn () => $this->activeTab === 'typography')
                    ->collapsible()
                    ->collapsed(),

                Section::make('Line Heights')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('typography.line_height_tight')
                                    ->label('Tight')
                                    ->numeric()
                                    ->step(0.01),
                                TextInput::make('typography.line_height_normal')
                                    ->label('Normal')
                                    ->numeric()
                                    ->step(0.01),
                                TextInput::make('typography.line_height_relaxed')
                                    ->label('Relaxed')
                                    ->numeric()
                                    ->step(0.01),
                            ]),
                    ])
                    ->visible(fn () => $this->activeTab === 'typography')
                    ->collapsible()
                    ->collapsed(),

                // Legacy tab form
                Section::make('Legacy Variables')
                    ->schema([
                        TextInput::make('legacy.items_per_row')
                            ->label('Items per Row')
                            ->helperText('Default items per row for grid layouts')
                            ->numeric()
                            ->default(4),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('legacy.spacing_small_value')
                                    ->label('Small Spacing')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(0.5),
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
                                    ->default('rem')
                                    ->required(),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('legacy.spacing_medium_value')
                                    ->label('Medium Spacing')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(1),
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
                                    ->default('rem')
                                    ->required(),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('legacy.spacing_large_value')
                                    ->label('Large Spacing')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(2),
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
                                    ->default('rem')
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
            'general' => [
                // Breakpoints
                ['key' => 'general.breakpoint.sm', 'value' => $data['general']['breakpoint_sm'] ?? '640px', 'type' => 'string'],
                ['key' => 'general.breakpoint.md', 'value' => $data['general']['breakpoint_md'] ?? '768px', 'type' => 'string'],
                ['key' => 'general.breakpoint.lg', 'value' => $data['general']['breakpoint_lg'] ?? '1024px', 'type' => 'string'],
                ['key' => 'general.breakpoint.xl', 'value' => $data['general']['breakpoint_xl'] ?? '1280px', 'type' => 'string'],
                ['key' => 'general.breakpoint.2xl', 'value' => $data['general']['breakpoint_2xl'] ?? '1536px', 'type' => 'string'],
                // Colors
                ['key' => 'general.color.primary', 'value' => $data['general']['color_primary'] ?? '#3b82f6', 'type' => 'string'],
                ['key' => 'general.color.secondary', 'value' => $data['general']['color_secondary'] ?? '#8b5cf6', 'type' => 'string'],
                ['key' => 'general.color.accent', 'value' => $data['general']['color_accent'] ?? '#10b981', 'type' => 'string'],
                ['key' => 'general.color.success', 'value' => $data['general']['color_success'] ?? '#22c55e', 'type' => 'string'],
                ['key' => 'general.color.warning', 'value' => $data['general']['color_warning'] ?? '#f59e0b', 'type' => 'string'],
                ['key' => 'general.color.danger', 'value' => $data['general']['color_danger'] ?? '#ef4444', 'type' => 'string'],
                ['key' => 'general.color.info', 'value' => $data['general']['color_info'] ?? '#06b6d4', 'type' => 'string'],
                ['key' => 'general.color.background', 'value' => $data['general']['color_background'] ?? '#ffffff', 'type' => 'string'],
                ['key' => 'general.color.text', 'value' => $data['general']['color_text'] ?? '#1f2937', 'type' => 'string'],
                // Shadows
                ['key' => 'general.shadow.sm', 'value' => $data['general']['shadow_sm'] ?? '0 1px 2px 0 rgb(0 0 0 / 0.05)', 'type' => 'string'],
                ['key' => 'general.shadow.md', 'value' => $data['general']['shadow_md'] ?? '0 4px 6px -1px rgb(0 0 0 / 0.1)', 'type' => 'string'],
                ['key' => 'general.shadow.lg', 'value' => $data['general']['shadow_lg'] ?? '0 10px 15px -3px rgb(0 0 0 / 0.1)', 'type' => 'string'],
                ['key' => 'general.shadow.xl', 'value' => $data['general']['shadow_xl'] ?? '0 20px 25px -5px rgb(0 0 0 / 0.1)', 'type' => 'string'],
                // Border Radius
                ['key' => 'general.radius.sm', 'value' => $data['general']['radius_sm'] ?? '0.125rem', 'type' => 'string'],
                ['key' => 'general.radius.md', 'value' => $data['general']['radius_md'] ?? '0.375rem', 'type' => 'string'],
                ['key' => 'general.radius.lg', 'value' => $data['general']['radius_lg'] ?? '0.5rem', 'type' => 'string'],
                ['key' => 'general.radius.xl', 'value' => $data['general']['radius_xl'] ?? '0.75rem', 'type' => 'string'],
                ['key' => 'general.radius.full', 'value' => $data['general']['radius_full'] ?? '9999px', 'type' => 'string'],
                // Spacing
                ['key' => 'general.spacing.xs', 'value' => $data['general']['spacing_xs'] ?? '0.25rem', 'type' => 'string'],
                ['key' => 'general.spacing.sm', 'value' => $data['general']['spacing_sm'] ?? '0.5rem', 'type' => 'string'],
                ['key' => 'general.spacing.md', 'value' => $data['general']['spacing_md'] ?? '1rem', 'type' => 'string'],
                ['key' => 'general.spacing.lg', 'value' => $data['general']['spacing_lg'] ?? '1.5rem', 'type' => 'string'],
                ['key' => 'general.spacing.xl', 'value' => $data['general']['spacing_xl'] ?? '2rem', 'type' => 'string'],
                ['key' => 'general.spacing.2xl', 'value' => $data['general']['spacing_2xl'] ?? '3rem', 'type' => 'string'],
            ],
            'typography' => [
                // Font Families
                ['key' => 'typography.font.primary', 'value' => $data['typography']['font_primary'] ?? 'Inter', 'type' => 'string'],
                ['key' => 'typography.font.secondary', 'value' => $data['typography']['font_secondary'] ?? 'Inter', 'type' => 'string'],
                ['key' => 'typography.font.heading', 'value' => $data['typography']['font_heading'] ?? 'Inter', 'type' => 'string'],
                ['key' => 'typography.font.body', 'value' => $data['typography']['font_body'] ?? 'Inter', 'type' => 'string'],
                // Font Sizes
                ['key' => 'typography.size.xs', 'value' => $data['typography']['font_size_xs'] ?? '0.75rem', 'type' => 'string'],
                ['key' => 'typography.size.sm', 'value' => $data['typography']['font_size_sm'] ?? '0.875rem', 'type' => 'string'],
                ['key' => 'typography.size.base', 'value' => $data['typography']['font_size_base'] ?? '1rem', 'type' => 'string'],
                ['key' => 'typography.size.lg', 'value' => $data['typography']['font_size_lg'] ?? '1.125rem', 'type' => 'string'],
                ['key' => 'typography.size.xl', 'value' => $data['typography']['font_size_xl'] ?? '1.25rem', 'type' => 'string'],
                ['key' => 'typography.size.2xl', 'value' => $data['typography']['font_size_2xl'] ?? '1.5rem', 'type' => 'string'],
                ['key' => 'typography.size.3xl', 'value' => $data['typography']['font_size_3xl'] ?? '1.875rem', 'type' => 'string'],
                ['key' => 'typography.size.4xl', 'value' => $data['typography']['font_size_4xl'] ?? '2.25rem', 'type' => 'string'],
                // Line Heights
                ['key' => 'typography.line_height.tight', 'value' => $data['typography']['line_height_tight'] ?? '1.25', 'type' => 'number'],
                ['key' => 'typography.line_height.normal', 'value' => $data['typography']['line_height_normal'] ?? '1.5', 'type' => 'number'],
                ['key' => 'typography.line_height.relaxed', 'value' => $data['typography']['line_height_relaxed'] ?? '1.75', 'type' => 'number'],
            ],
            'legacy' => [
                ['key' => 'legacy.items_per_row', 'value' => $data['legacy']['items_per_row'] ?? 4, 'type' => 'number'],
                ['key' => 'legacy.spacing.small', 'value' => ($data['legacy']['spacing_small_value'] ?? 0.5).($data['legacy']['spacing_small_unit'] ?? 'rem'), 'type' => 'string'],
                ['key' => 'legacy.spacing.medium', 'value' => ($data['legacy']['spacing_medium_value'] ?? 1).($data['legacy']['spacing_medium_unit'] ?? 'rem'), 'type' => 'string'],
                ['key' => 'legacy.spacing.large', 'value' => ($data['legacy']['spacing_large_value'] ?? 2).($data['legacy']['spacing_large_unit'] ?? 'rem'), 'type' => 'string'],
            ],
            'si' => [
                ['key' => 'si.length_classes', 'value' => $data['si']['length_classes'] ?? [], 'type' => 'json'],
                ['key' => 'si.weight_classes', 'value' => $data['si']['weight_classes'] ?? [], 'type' => 'json'],
            ],
            default => [],
        };

        foreach ($variablesToSave as $varData) {
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
     * Get popular Google Fonts options
     */
    protected function getGoogleFontsOptions(): array
    {
        return [
            'Inter' => 'Inter',
            'Roboto' => 'Roboto',
            'Open Sans' => 'Open Sans',
            'Lato' => 'Lato',
            'Montserrat' => 'Montserrat',
            'Poppins' => 'Poppins',
            'Raleway' => 'Raleway',
            'Oswald' => 'Oswald',
            'Source Sans Pro' => 'Source Sans Pro',
            'Playfair Display' => 'Playfair Display',
            'Merriweather' => 'Merriweather',
            'Ubuntu' => 'Ubuntu',
            'Nunito' => 'Nunito',
            'Cabin' => 'Cabin',
            'Dancing Script' => 'Dancing Script',
            'Pacifico' => 'Pacifico',
            'Bebas Neue' => 'Bebas Neue',
            'Outfit' => 'Outfit',
            'Work Sans' => 'Work Sans',
            'DM Sans' => 'DM Sans',
            'Plus Jakarta Sans' => 'Plus Jakarta Sans',
            'Manrope' => 'Manrope',
            'Space Grotesk' => 'Space Grotesk',
            'Sora' => 'Sora',
            'Epilogue' => 'Epilogue',
            'Figtree' => 'Figtree',
            'Geist' => 'Geist',
            'Geist Sans' => 'Geist Sans',
            'Geist Mono' => 'Geist Mono',
        ];
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

        return $filtered;
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
}
