<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domain\Catalog\Models\FilterValue;
use App\Filament\Resources\FilterValueResource\Pages;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class FilterValueResource extends Resource
{
    protected static ?string $model = FilterValue::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-funnel';

    protected static ?string $navigationLabel = 'Filter Values';

    protected static ?string $modelLabel = 'Filter Value';

    protected static ?string $pluralModelLabel = 'Filter Values';

    protected static string|\UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->columns(2)
                    ->schema([
                        Select::make('filter_group_id')
                            ->label('Filter Group')
                            ->relationship('group', 'name', fn (Builder $query) => $query->where('is_active', true))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('The filter group this value belongs to'),
                        TextInput::make('value')
                            ->label('Value')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('slug', Str::slug($state));
                            })
                            ->helperText('Filter value (e.g., Red, Blue, Small, Large)'),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->nullable()
                            ->maxLength(255)
                            ->helperText('URL-friendly identifier (auto-generated from value)'),
                        TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required()
                            ->helperText('Lower numbers appear first'),
                    ]),
                Section::make('Display')
                    ->columns(2)
                    ->schema([
                        ColorPicker::make('color')
                            ->label('Color')
                            ->nullable()
                            ->helperText('Optional color for display (e.g., #FF0000 for red)'),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Only active filter values are visible on the public site'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('value')
                    ->label('Value')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('group.name')
                    ->label('Filter Group')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                ColorColumn::make('color')
                    ->label('Color')
                    ->toggleable(),
                TextColumn::make('products_count')
                    ->label('Products')
                    ->counts('products')
                    ->sortable()
                    ->badge()
                    ->color('success'),
                TextColumn::make('sort_order')
                    ->label('Sort Order')
                    ->sortable()
                    ->alignCenter(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('filter_group_id')
                    ->label('Filter Group')
                    ->relationship('group', 'name', fn (Builder $query) => $query->where('is_active', true))
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
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
            ->with(['group', 'products']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFilterValues::route('/'),
            'create' => Pages\CreateFilterValue::route('/create'),
            'edit' => Pages\EditFilterValue::route('/{record}/edit'),
        ];
    }
}
