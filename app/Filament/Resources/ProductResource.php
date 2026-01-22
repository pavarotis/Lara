<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domain\Catalog\Models\Product;
use App\Filament\Resources\ProductResource\Pages;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Products';

    protected static ?string $modelLabel = 'Product';

    protected static ?string $pluralModelLabel = 'Products';

    protected static string|\UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 2;

    /**
     * Use a custom slug to avoid clashing with the legacy /admin/products routes.
     * This will register the Filament resource at /admin/catalog-products with route name
     * filament.admin.resources.catalog-products.index.
     */
    public static function getSlug(?\Filament\Panel $panel = null): string
    {
        return 'catalog-products';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->columns(2)
                    ->schema([
                        Select::make('business_id')
                            ->label('Business')
                            ->relationship('business', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->default(fn () => \App\Domain\Businesses\Models\Business::active()->first()?->id)
                            ->helperText('The business this product belongs to'),
                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name', fn (Builder $query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Product category'),
                        Select::make('manufacturer_id')
                            ->label('Manufacturer')
                            ->relationship('manufacturer', 'name', fn (Builder $query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Product manufacturer (optional)'),
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('slug', Str::slug($state));
                            })
                            ->helperText('Product name'),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('URL-friendly identifier (auto-generated from name)'),
                        TextInput::make('price')
                            ->label('Price')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01)
                            ->minValue(0)
                            ->required()
                            ->helperText('Product price'),
                        TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required()
                            ->helperText('Lower numbers appear first'),
                    ]),
                Section::make('Content')
                    ->schema([
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(4)
                            ->maxLength(65535)
                            ->columnSpanFull()
                            ->helperText('Product description'),
                    ]),
                Section::make('Media')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Image')
                            ->image()
                            ->directory('products')
                            ->maxSize(5120)
                            ->helperText('Product image (max 5MB)'),
                        TextInput::make('image_alt')
                            ->label('Image Alt Text')
                            ->maxLength(255)
                            ->helperText('Alternative text for the image (important for SEO and accessibility)'),
                        TextInput::make('image_title')
                            ->label('Image Title')
                            ->maxLength(255)
                            ->helperText('Title attribute for the image (shown on hover)'),
                    ]),
                Section::make('Filters')
                    ->description('Assign filter values to this product for filtering on the public site.')
                    ->schema([
                        CheckboxList::make('filterValues')
                            ->label('Product Filters')
                            ->relationship('filterValues', 'value', fn (Builder $query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->columns(2)
                            ->helperText('Select filter values that apply to this product'),
                    ]),
                Section::make('Attributes')
                    ->description('Assign attribute values to this product for specifications/details.')
                    ->schema([
                        Repeater::make('productAttributes')
                            ->label('Product Attributes')
                            ->schema([
                                Select::make('attribute_id')
                                    ->label('Attribute')
                                    ->options(function () {
                                        return \App\Domain\Catalog\Models\Attribute::where('is_active', true)
                                            ->with('group')
                                            ->get()
                                            ->mapWithKeys(function ($attr) {
                                                return [$attr->id => ($attr->group->name ?? 'Unknown').' - '.$attr->name];
                                            })
                                            ->toArray();
                                    })
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->helperText('Select an attribute'),
                                TextInput::make('value')
                                    ->label('Value')
                                    ->required()
                                    ->maxLength(65535)
                                    ->helperText('Attribute value for this product'),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => \App\Domain\Catalog\Models\Attribute::find($state['attribute_id'] ?? null)?->name ?? 'New Attribute')
                            ->collapsible()
                            ->defaultItems(0)
                            ->addActionLabel('Add Attribute')
                            ->reorderable()
                            ->deletable()
                            ->helperText('Add attribute values for this product (e.g., Material: Cotton, Weight: 500g)')
                            ->dehydrated(false)
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record && $record->exists) {
                                    $attributes = $record->attributes()->with('group')->get()->map(function ($attribute) {
                                        return [
                                            'attribute_id' => $attribute->id,
                                            'value' => $attribute->pivot->value ?? '',
                                        ];
                                    })->toArray();
                                    $component->state($attributes);
                                }
                            }),
                    ]),
                Section::make('SEO Settings')
                    ->description('Search engine optimization settings for this product')
                    ->collapsible()
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(60)
                            ->helperText('Recommended: 50-60 characters. Leave empty to use product name.'),
                        Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->maxLength(160)
                            ->rows(3)
                            ->helperText('Recommended: 150-160 characters. Leave empty to use product description.'),
                        Textarea::make('meta_keywords')
                            ->label('Meta Keywords')
                            ->rows(2)
                            ->helperText('Comma-separated keywords for this product'),
                    ]),
                Section::make('Settings')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_available')
                            ->label('Available')
                            ->default(true)
                            ->helperText('Only available products are visible on the public site'),
                        Toggle::make('is_featured')
                            ->label('Featured')
                            ->default(false)
                            ->helperText('Featured products are highlighted on the public site'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->color('gray')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('manufacturer.name')
                    ->label('Manufacturer')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->toggleable(),
                TextColumn::make('price')
                    ->label('Price')
                    ->money('EUR')
                    ->sortable()
                    ->alignEnd(),
                TextColumn::make('sort_order')
                    ->label('Sort Order')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_available')
                    ->label('Available')
                    ->boolean()
                    ->sortable(),
                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('business.name')
                    ->label('Business')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('extras_count')
                    ->label('Extras')
                    ->counts('extras')
                    ->badge()
                    ->color('success')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('business_id')
                    ->label('Business')
                    ->relationship('business', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name', fn (Builder $query) => $query->where('is_active', true))
                    ->searchable()
                    ->preload(),
                SelectFilter::make('manufacturer_id')
                    ->label('Manufacturer')
                    ->relationship('manufacturer', 'name', fn (Builder $query) => $query->where('is_active', true))
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('is_available')
                    ->label('Available')
                    ->placeholder('All')
                    ->trueLabel('Available only')
                    ->falseLabel('Unavailable only'),
                TernaryFilter::make('is_featured')
                    ->label('Featured')
                    ->placeholder('All')
                    ->trueLabel('Featured only')
                    ->falseLabel('Not featured'),
            ])
            ->actions([
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil'),
                DeleteAction::make()
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['business', 'category', 'manufacturer', 'extras', 'filterValues.group', 'attributes.group']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
