<?php

namespace App\Filament\Resources;

use App\Domain\Modules\Models\ModuleInstance;
use App\Filament\Resources\ModuleInstanceResource\Pages;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
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

class ModuleInstanceResource extends Resource
{
    protected static ?string $model = ModuleInstance::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $navigationLabel = 'Modules';

    protected static ?string $modelLabel = 'Module Instance';

    protected static ?string $pluralModelLabel = 'Module Instances';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    public static function form(Schema $schema): Schema
    {
        $moduleTypes = collect(config('modules', []))->mapWithKeys(function ($config, $key) {
            return [$key => $config['name'] ?? $key];
        })->toArray();

        return $schema
            ->components([
                Section::make('Basic Information')
                    ->components([
                        Select::make('business_id')
                            ->label('Business')
                            ->relationship('business', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->default(function () {
                                return \App\Domain\Businesses\Models\Business::active()->first()?->id;
                            }),
                        Select::make('type')
                            ->label('Module Type')
                            ->options($moduleTypes)
                            ->required()
                            ->searchable(),
                        TextInput::make('name')
                            ->label('Name (Optional - Makes module reusable)')
                            ->helperText('Leave blank for page-specific modules. Set a name to make this module reusable across multiple pages.')
                            ->maxLength(255),
                        Toggle::make('enabled')
                            ->label('Enabled')
                            ->default(true)
                            ->required(),
                    ]),
                Section::make('Settings')
                    ->components([
                        KeyValue::make('settings')
                            ->label('Module Settings')
                            ->helperText('Settings are stored as JSON. Configure according to module type.')
                            ->keyLabel('Key')
                            ->valueLabel('Value'),
                    ]),
                Section::make('Style & Layout')
                    ->components([
                        Select::make('width_mode')
                            ->label('Width Mode')
                            ->options([
                                'contained' => 'Contained',
                                'full' => 'Full Width',
                                'full-bg-contained-content' => 'Full Background, Contained Content',
                            ])
                            ->default('contained')
                            ->required(),
                        KeyValue::make('style')
                            ->label('Style (JSON)')
                            ->helperText('Style properties like background, padding, margin. Format: background, background_image, padding, margin')
                            ->keyLabel('Property')
                            ->valueLabel('Value'),
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
                TextColumn::make('type')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('business.name')
                    ->label('Business')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('enabled')
                    ->label('Enabled')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('width_mode')
                    ->label('Width Mode')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'full' => 'success',
                        'full-bg-contained-content' => 'warning',
                        default => 'info',
                    }),
                TextColumn::make('assignments_count')
                    ->label('Used In')
                    ->counts('assignments')
                    ->sortable(),
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
                    ->options(collect(config('modules', []))->mapWithKeys(function ($config, $key) {
                        return [$key => $config['name'] ?? $key];
                    })->toArray()),
                TernaryFilter::make('enabled')
                    ->label('Enabled')
                    ->placeholder('All')
                    ->trueLabel('Enabled only')
                    ->falseLabel('Disabled only'),
                SelectFilter::make('business_id')
                    ->label('Business')
                    ->relationship('business', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (ModuleInstance $record) {
                        // Check if module is assigned to any content
                        if ($record->assignments()->count() > 0) {
                            throw new \Exception('Cannot delete module that is assigned to content. Remove assignments first.');
                        }
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if ($record->assignments()->count() > 0) {
                                    throw new \Exception('Cannot delete modules that are assigned to content. Remove assignments first.');
                                }
                            }
                        }),
                ]),
            ]);
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
            'index' => Pages\ListModuleInstances::route('/'),
            'create' => Pages\CreateModuleInstance::route('/create'),
            'edit' => Pages\EditModuleInstance::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['business', 'assignments']);

        // Scope by current business if available
        $business = \App\Domain\Businesses\Models\Business::active()->first();
        if ($business) {
            $query->forBusiness($business->id);
        }

        return $query;
    }
}
