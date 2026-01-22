<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domain\Sales\Models\Tax;
use App\Filament\Resources\TaxResource\Pages;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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

class TaxResource extends Resource
{
    protected static ?string $model = Tax::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationLabel = 'Taxes';

    protected static ?string $modelLabel = 'Tax';

    protected static ?string $pluralModelLabel = 'Taxes';

    protected static string|\UnitEnum|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tax Information')
                    ->columns(2)
                    ->schema([
                        Select::make('business_id')
                            ->label('Business')
                            ->relationship('business', 'name')
                            ->searchable()
                            ->preload()
                            ->default(fn () => \App\Domain\Businesses\Models\Business::active()->first()?->id)
                            ->helperText('The business this tax applies to (leave empty for global)'),
                        TextInput::make('name')
                            ->label('Tax Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., VAT, Sales Tax')
                            ->helperText('Name of the tax rate'),
                        Select::make('type')
                            ->label('Tax Type')
                            ->options([
                                'percentage' => 'Percentage',
                                'fixed' => 'Fixed Amount',
                            ])
                            ->required()
                            ->default('percentage')
                            ->helperText('Percentage or fixed amount'),
                        TextInput::make('rate')
                            ->label('Tax Rate')
                            ->numeric()
                            ->step(0.0001)
                            ->required()
                            ->default(0)
                            ->placeholder('e.g., 24.0000 for 24%')
                            ->helperText('Tax rate (e.g., 24.0000 for 24% VAT)'),
                        Select::make('geo_zone_id')
                            ->label('Geo Zone')
                            ->relationship('geoZone', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Optional: Apply this tax only to specific geo zones'),
                        TextInput::make('priority')
                            ->label('Priority')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->maxValue(100)
                            ->required()
                            ->helperText('Priority order when multiple taxes apply (lower = higher priority)'),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Enable or disable this tax rate'),
                    ]),
                Section::make('Description')
                    ->schema([
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->maxLength(65535)
                            ->columnSpanFull()
                            ->placeholder('Optional description for this tax rate')
                            ->helperText('Additional information about this tax'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tax Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'percentage' => 'info',
                        'fixed' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'percentage' => 'Percentage',
                        'fixed' => 'Fixed',
                        default => $state,
                    })
                    ->sortable(),
                TextColumn::make('rate')
                    ->label('Rate')
                    ->formatStateUsing(function ($state, Tax $record): string {
                        if ($record->type === 'percentage') {
                            return number_format((float) $state, 2).'%';
                        }

                        return '€'.number_format((float) $state, 2);
                    })
                    ->sortable(),
                TextColumn::make('geoZone.name')
                    ->label('Geo Zone')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->placeholder('Global'),
                TextColumn::make('priority')
                    ->label('Priority')
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('business.name')
                    ->label('Business')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('Global'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'percentage' => 'Percentage',
                        'fixed' => 'Fixed',
                    ]),
                SelectFilter::make('geo_zone_id')
                    ->label('Geo Zone')
                    ->relationship('geoZone', 'name')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
                SelectFilter::make('business_id')
                    ->label('Business')
                    ->relationship('business', 'name')
                    ->searchable()
                    ->preload(),
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
            ->defaultSort('priority', 'asc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['business', 'geoZone']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTaxes::route('/'),
            'create' => Pages\CreateTax::route('/create'),
            'edit' => Pages\EditTax::route('/{record}/edit'),
        ];
    }
}
