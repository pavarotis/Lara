<?php

declare(strict_types=1);

namespace App\Filament\Pages\CMS;

use App\Domain\Businesses\Models\Business;
use App\Domain\Variables\Models\Variable;
use App\Domain\Variables\Services\ThemeService;
use App\Domain\Variables\Services\VariableService;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class DynamicSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.cms.dynamic-settings';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?string $navigationLabel = 'Dynamic Settings';

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

        // Load all variables from database and populate form data
        $this->loadVariables($business);
    }

    /**
     * Load variables from database and populate form data
     */
    protected function loadVariables(Business $business): void
    {
        $variables = Variable::forBusiness($business->id)
            ->orderBy('category')
            ->orderBy('key')
            ->get();

        $formData = [];

        foreach ($variables as $variable) {
            $formData[$variable->key] = $this->castValueForForm($variable);
        }

        $this->form->fill($formData);
    }

    /**
     * Cast variable value based on type for form display
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
     * Build form schema dynamically from database variables
     */
    public function form(Schema $schema): Schema
    {
        $business = Business::active()->first();
        if (! $business) {
            return $schema->components([]);
        }

        // Get all variables grouped by category
        $variables = Variable::forBusiness($business->id)
            ->orderBy('category')
            ->orderBy('key')
            ->get()
            ->groupBy('category');

        // If no variables, return empty schema
        if ($variables->isEmpty()) {
            return $schema->components([
                Section::make('No Variables')
                    ->description('No variables found in database. Add variables to see them here.')
                    ->schema([]),
            ]);
        }

        // Build tabs from categories
        $tabs = [];
        foreach ($variables as $category => $categoryVariables) {
            $sections = $this->buildSectionsForCategory($categoryVariables);
            $tabs[] = Tab::make(ucfirst($category))
                ->label(ucfirst(str_replace('_', ' ', $category)))
                ->icon($this->getCategoryIcon($category))
                ->schema($sections);
        }

        return $schema->components([
            Tabs::make('SettingsTabs')
                ->tabs($tabs),
        ]);
    }

    /**
     * Build sections for a category (can group by description or keep flat)
     */
    protected function buildSectionsForCategory($variables): array
    {
        $fields = [];

        foreach ($variables as $variable) {
            $field = $this->createFieldForVariable($variable);
            if ($field) {
                $fields[] = $field;
            }
        }

        // Group fields in a single section per category
        return [
            Section::make(ucfirst($variables->first()->category))
                ->description('Manage settings for '.ucfirst(str_replace('_', ' ', $variables->first()->category)))
                ->schema($fields)
                ->collapsible()
                ->collapsed(false),
        ];
    }

    /**
     * Create form field based on variable type
     */
    protected function createFieldForVariable(Variable $variable)
    {
        $label = $variable->description ?: ucfirst(str_replace('_', ' ', $variable->key));
        $helperText = $variable->description ? "Key: {$variable->key}" : null;

        return match ($variable->type) {
            'string' => TextInput::make($variable->key)
                ->label($label)
                ->helperText($helperText)
                ->placeholder('Enter value...')
                ->maxLength(255),

            'number' => TextInput::make($variable->key)
                ->label($label)
                ->helperText($helperText)
                ->numeric()
                ->step(0.01)
                ->placeholder('0'),

            'boolean' => Toggle::make($variable->key)
                ->label($label)
                ->helperText($helperText)
                ->default(false),

            'json' => Textarea::make($variable->key)
                ->label($label)
                ->helperText($helperText ? $helperText.' (JSON format)' : 'JSON format')
                ->placeholder('{"key": "value"}')
                ->rows(4)
                ->extraAttributes([
                    'style' => 'font-family: monospace;',
                ]),

            default => TextInput::make($variable->key)
                ->label($label)
                ->helperText($helperText),
        };
    }

    /**
     * Get icon for category
     */
    protected function getCategoryIcon(string $category): string
    {
        return match ($category) {
            // General & Site
            'general' => 'heroicon-o-cog-6-tooth',
            'appearance' => 'heroicon-o-paint-brush',
            'seo' => 'heroicon-o-magnifying-glass',
            'email' => 'heroicon-o-envelope',
            'social' => 'heroicon-o-share',
            'payment' => 'heroicon-o-credit-card',
            'shipping' => 'heroicon-o-truck',

            // Admin Categories
            'dashboard' => 'heroicon-o-chart-bar',
            'cache' => 'heroicon-o-arrow-path',
            'blog' => 'heroicon-o-document-text',
            'cms' => 'heroicon-o-squares-2x2',
            'catalog' => 'heroicon-o-shopping-bag',
            'catalog_spare' => 'heroicon-o-archive-box',
            'extensions' => 'heroicon-o-puzzle-piece',
            'sales' => 'heroicon-o-currency-dollar',
            'customers' => 'heroicon-o-users',
            'marketing' => 'heroicon-o-megaphone',
            'system' => 'heroicon-o-cpu-chip',
            'reports' => 'heroicon-o-chart-pie',

            default => 'heroicon-o-adjustments-horizontal',
        };
    }

    /**
     * Save all form data back to database
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

        foreach ($data as $key => $value) {
            $variable = Variable::forBusiness($business->id)
                ->where('key', $key)
                ->first();

            if ($variable) {
                // Cast value based on type before saving
                $valueToSave = match ($variable->type) {
                    'number' => (string) $value,
                    'boolean' => $value ? '1' : '0',
                    'json' => is_string($value) ? $value : json_encode($value),
                    default => (string) $value,
                };

                $variable->update(['value' => $valueToSave]);
            }
        }

        // Clear all caches using services
        $variableService = app(VariableService::class);
        $variableService->clearCache($business);

        $themeService = app(ThemeService::class);
        $themeService->clearCache($business);

        Notification::make()
            ->success()
            ->title('Settings saved successfully')
            ->body('All variables have been updated and caches cleared.')
            ->send();
    }

    public static function getSlug(?\Filament\Panel $panel = null): string
    {
        return 'dynamic-settings';
    }

    public function getTitle(): string
    {
        return 'Dynamic Settings';
    }
}
