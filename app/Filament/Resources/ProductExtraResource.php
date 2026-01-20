<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Models\ProductExtra;
use App\Filament\Resources\ProductExtraResource\Pages;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductExtraResource extends Resource
{
    protected static ?string $model = ProductExtra::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'Product Extras';

    protected static ?string $modelLabel = 'Product Extra';

    protected static ?string $pluralModelLabel = 'Product Extras';

    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 9;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->components([
                        Select::make('product_id')
                            ->label('Product')
                            ->relationship('product', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->default(function () {
                                return Product::whereHas('business', function ($q) {
                                    $q->where('is_active', true);
                                })->first()?->id;
                            }),
                        TextInput::make('key')
                            ->label('Key')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Extra key (e.g., size, color, addon). Use lowercase with underscores.')
                            ->rules(['regex:/^[a-z0-9_]+$/']),
                        TextInput::make('label')
                            ->label('Display Label')
                            ->maxLength(255)
                            ->helperText('Optional label for display (defaults to formatted key)'),
                        Select::make('type')
                            ->label('Type')
                            ->options([
                                'string' => 'String',
                                'number' => 'Number',
                                'boolean' => 'Boolean',
                                'json' => 'JSON',
                            ])
                            ->default('string')
                            ->required()
                            ->live(),
                        TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Order for display (lower numbers appear first)'),
                    ]),
                Section::make('Value')
                    ->components([
                        TextInput::make('value')
                            ->label('Value')
                            ->visible(fn ($get) => in_array($get('type'), ['string', 'number']))
                            ->required(fn ($get) => in_array($get('type'), ['string', 'number']))
                            ->numeric(fn ($get) => $get('type') === 'number')
                            ->helperText(fn ($get) => $get('type') === 'number' ? 'Enter a number' : 'Enter text value'),
                        Toggle::make('value')
                            ->label('Value')
                            ->visible(fn ($get) => $get('type') === 'boolean')
                            ->helperText('Toggle for boolean value'),
                        CodeEditor::make('value')
                            ->label('Value (JSON)')
                            ->visible(fn ($get) => $get('type') === 'json')
                            ->json()
                            ->required(fn ($get) => $get('type') === 'json')
                            ->helperText('Enter valid JSON'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('key')
                    ->label('Key')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('label')
                    ->label('Label')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('value')
                    ->label('Value')
                    ->limit(50)
                    ->searchable()
                    ->formatStateUsing(function (ProductExtra $record) {
                        if ($record->type === 'boolean') {
                            return $record->getTypedValue() ? 'Yes' : 'No';
                        }
                        if ($record->type === 'json') {
                            return json_encode($record->getTypedValue());
                        }

                        return $record->value;
                    }),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'string' => 'success',
                        'number' => 'info',
                        'boolean' => 'warning',
                        'json' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'string' => 'String',
                        'number' => 'Number',
                        'boolean' => 'Boolean',
                        'json' => 'JSON',
                    ]),
                SelectFilter::make('product_id')
                    ->label('Product')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductExtras::route('/'),
            'create' => Pages\CreateProductExtra::route('/create'),
            'edit' => Pages\EditProductExtra::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['product'])
            ->ordered();
    }
}
