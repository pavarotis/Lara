<?php

declare(strict_types=1);

namespace App\Filament\Pages\Catalog;

use App\Domain\Businesses\Models\Business;
use App\Domain\Variables\Models\Variable;
use App\Domain\Variables\Services\VariableService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Cache;

class Options extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 7;

    protected string $view = 'filament.pages.catalog.options';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationLabel = 'Options';

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

        // Load options-related variables
        $this->loadVariables($business);
    }

    /**
     * Load variables from database
     */
    protected function loadVariables(Business $business): void
    {
        $variables = Variable::forBusiness($business->id)
            ->where('category', 'catalog')
            ->whereIn('key', [
                'catalog_options_enabled',
                'catalog_options_default_type',
                'catalog_options_required_by_default',
                'catalog_options_sort_order',
            ])
            ->get();

        $formData = [];

        foreach ($variables as $variable) {
            $formData[$variable->key] = $this->castValueForForm($variable);
        }

        // Set defaults if variables don't exist
        $formData['catalog_options_enabled'] = $formData['catalog_options_enabled'] ?? true;
        $formData['catalog_options_default_type'] = $formData['catalog_options_default_type'] ?? 'select';
        $formData['catalog_options_required_by_default'] = $formData['catalog_options_required_by_default'] ?? false;
        $formData['catalog_options_sort_order'] = $formData['catalog_options_sort_order'] ?? 0;

        $this->form->fill($formData);
    }

    /**
     * Cast variable value for form display
     */
    protected function castValueForForm(Variable $variable): mixed
    {
        return match ($variable->type) {
            'number' => is_numeric($variable->value) ? (str_contains($variable->value, '.') ? (float) $variable->value : (int) $variable->value) : 0,
            'boolean' => filter_var($variable->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($variable->value, true) ?? [],
            default => $variable->value ?? '',
        };
    }

    /**
     * Build form schema
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Product Options Settings')
                    ->description('Configure default settings for product options (e.g., Size, Color, Material). Options can be managed per product in the Product edit form.')
                    ->schema([
                        Toggle::make('catalog_options_enabled')
                            ->label('Enable Product Options')
                            ->helperText('Allow products to have custom options (Size, Color, etc.)')
                            ->default(true)
                            ->live(),

                        Select::make('catalog_options_default_type')
                            ->label('Default Option Type')
                            ->options([
                                'select' => 'Select (Dropdown)',
                                'radio' => 'Radio Buttons',
                                'checkbox' => 'Checkbox',
                                'text' => 'Text Input',
                                'textarea' => 'Textarea',
                                'number' => 'Number',
                                'date' => 'Date',
                            ])
                            ->default('select')
                            ->helperText('Default type for new product options')
                            ->required()
                            ->visible(fn (callable $get) => $get('catalog_options_enabled')),

                        Toggle::make('catalog_options_required_by_default')
                            ->label('Required by Default')
                            ->helperText('New options will be required by default')
                            ->default(false)
                            ->visible(fn (callable $get) => $get('catalog_options_enabled')),

                        TextInput::make('catalog_options_sort_order')
                            ->label('Default Sort Order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Default sort order for new options')
                            ->visible(fn (callable $get) => $get('catalog_options_enabled')),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                Section::make('Information')
                    ->description('Product Options allow you to add customizable fields to products (e.g., Size: Small/Medium/Large, Color: Red/Blue/Green). These options are managed per product in the Product edit form.')
                    ->schema([])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    /**
     * Save settings
     */
    public function save(): void
    {
        $business = Business::active()->first();
        if (! $business) {
            Notification::make()
                ->danger()
                ->title('No active business found')
                ->send();

            return;
        }

        $data = $this->form->getState();

        $variablesToSave = [
            [
                'key' => 'catalog_options_enabled',
                'value' => $data['catalog_options_enabled'] ?? true,
                'type' => 'boolean',
                'category' => 'catalog',
                'description' => 'Enable Product Options',
            ],
            [
                'key' => 'catalog_options_default_type',
                'value' => $data['catalog_options_default_type'] ?? 'select',
                'type' => 'string',
                'category' => 'catalog',
                'description' => 'Default Option Type',
            ],
            [
                'key' => 'catalog_options_required_by_default',
                'value' => $data['catalog_options_required_by_default'] ?? false,
                'type' => 'boolean',
                'category' => 'catalog',
                'description' => 'Required by Default',
            ],
            [
                'key' => 'catalog_options_sort_order',
                'value' => (string) ($data['catalog_options_sort_order'] ?? 0),
                'type' => 'number',
                'category' => 'catalog',
                'description' => 'Default Sort Order',
            ],
        ];

        foreach ($variablesToSave as $varData) {
            $variable = Variable::firstOrNew([
                'business_id' => $business->id,
                'key' => $varData['key'],
            ]);

            $variable->type = $varData['type'];
            $variable->category = $varData['category'];
            $variable->description = $varData['description'];

            // Cast value based on type
            $valueToSave = match ($varData['type']) {
                'number' => (string) $varData['value'],
                'boolean' => $varData['value'] ? '1' : '0',
                'json' => is_string($varData['value']) ? $varData['value'] : json_encode($varData['value']),
                default => (string) $varData['value'],
            };

            $variable->value = $valueToSave;
            $variable->save();

            // Clear cache
            Cache::forget("variable:{$business->id}:{$varData['key']}");
        }

        // Clear all variables cache
        $variableService = app(VariableService::class);
        $variableService->clearCache($business);

        Notification::make()
            ->success()
            ->title('Options settings saved successfully')
            ->body('Product options configuration has been updated.')
            ->send();
    }

    public static function getSlug(?\Filament\Panel $panel = null): string
    {
        return 'catalog-options';
    }

    public function getTitle(): string
    {
        return 'Product Options';
    }
}
