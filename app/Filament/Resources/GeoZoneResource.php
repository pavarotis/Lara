<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domain\Sales\Models\GeoZone;
use App\Filament\Resources\GeoZoneResource\Pages;
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

class GeoZoneResource extends Resource
{
    protected static ?string $model = GeoZone::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-americas';

    protected static ?string $navigationLabel = 'Geo Zones';

    protected static ?string $modelLabel = 'Geo Zone';

    protected static ?string $pluralModelLabel = 'Geo Zones';

    protected static string|\UnitEnum|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Geo Zone Information')
                    ->columns(2)
                    ->schema([
                        Select::make('business_id')
                            ->label('Business')
                            ->relationship('business', 'name')
                            ->searchable()
                            ->preload()
                            ->default(fn () => \App\Domain\Businesses\Models\Business::active()->first()?->id)
                            ->helperText('The business this geo zone belongs to (leave empty for global)'),
                        TextInput::make('name')
                            ->label('Geo Zone Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Europe, North America, Greece')
                            ->helperText('Name of the geographic zone'),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Enable or disable this geo zone'),
                    ]),
                Section::make('Zones')
                    ->description('Select countries or zones to include in this geo zone')
                    ->schema([
                        Select::make('zones')
                            ->label('Countries/Zones')
                            ->multiple()
                            ->options([
                                'GR' => 'Greece',
                                'US' => 'United States',
                                'UK' => 'United Kingdom',
                                'DE' => 'Germany',
                                'FR' => 'France',
                                'IT' => 'Italy',
                                'ES' => 'Spain',
                                'NL' => 'Netherlands',
                                'BE' => 'Belgium',
                                'PT' => 'Portugal',
                                'AT' => 'Austria',
                                'CH' => 'Switzerland',
                                'SE' => 'Sweden',
                                'NO' => 'Norway',
                                'DK' => 'Denmark',
                                'FI' => 'Finland',
                                'PL' => 'Poland',
                                'CZ' => 'Czech Republic',
                                'HU' => 'Hungary',
                                'RO' => 'Romania',
                                'BG' => 'Bulgaria',
                                'HR' => 'Croatia',
                                'TR' => 'Turkey',
                            ])
                            ->searchable()
                            ->helperText('Select countries or zones that belong to this geo zone'),
                    ]),
                Section::make('Description')
                    ->schema([
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->maxLength(65535)
                            ->columnSpanFull()
                            ->placeholder('Optional description for this geo zone')
                            ->helperText('Additional information about this geographic zone'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Geo Zone Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('zones')
                    ->label('Zones')
                    ->formatStateUsing(function ($state): string {
                        if (empty($state) || ! is_array($state)) {
                            return 'None';
                        }

                        return count($state).' zone(s)';
                    })
                    ->badge()
                    ->color('info'),
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
                TextColumn::make('taxes_count')
                    ->label('Taxes')
                    ->counts('taxes')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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
            ->defaultSort('name', 'asc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['business', 'taxes']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGeoZones::route('/'),
            'create' => Pages\CreateGeoZone::route('/create'),
            'edit' => Pages\EditGeoZone::route('/{record}/edit'),
        ];
    }
}
