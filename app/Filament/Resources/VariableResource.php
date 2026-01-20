<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domain\Variables\Models\Variable;
use App\Filament\Resources\VariableResource\Pages;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VariableResource extends Resource
{
    protected static ?string $model = Variable::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-variable';

    protected static ?string $navigationLabel = 'Variables';

    protected static ?string $modelLabel = 'Variable';

    protected static ?string $pluralModelLabel = 'Variables';

    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
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
                        TextInput::make('key')
                            ->label('Key')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Variable key (e.g., site_name, contact_email). Use lowercase with underscores.')
                            ->rules(['regex:/^[a-z0-9_]+$/']),
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
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(2)
                            ->maxLength(500)
                            ->helperText('Optional description for admin reference'),
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
                TextColumn::make('key')
                    ->label('Key')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('value')
                    ->label('Value')
                    ->limit(50)
                    ->searchable()
                    ->formatStateUsing(function (Variable $record) {
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
                TextColumn::make('business.name')
                    ->label('Business')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(30)
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
                SelectFilter::make('business_id')
                    ->label('Business')
                    ->relationship('business', 'name')
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
            'index' => Pages\ListVariables::route('/'),
            'create' => Pages\CreateVariable::route('/create'),
            'edit' => Pages\EditVariable::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['business']);

        // Scope by current business if available
        $business = \App\Domain\Businesses\Models\Business::active()->first();
        if ($business) {
            $query->forBusiness($business->id);
        }

        return $query;
    }
}
