<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domain\Layouts\Models\Layout;
use App\Filament\Resources\LayoutResource\Pages;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
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

class LayoutResource extends Resource
{
    protected static ?string $model = Layout::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Layouts';

    protected static ?string $modelLabel = 'Layout';

    protected static ?string $pluralModelLabel = 'Layouts';

    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        $availableRegions = [
            'header_top' => 'Header Top',
            'header_bottom' => 'Header Bottom',
            'content_top' => 'Content Top',
            'main_content' => 'Main Content',
            'content_bottom' => 'Content Bottom',
            'column_left' => 'Column Left',
            'column_right' => 'Column Right',
            'footer_top' => 'Footer Top',
            'footer_bottom' => 'Footer Bottom',
        ];

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
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),
                        Select::make('type')
                            ->label('Layout Type')
                            ->options([
                                'default' => 'Default',
                                'full-width' => 'Full Width',
                                'landing' => 'Landing',
                                'page' => 'Page',
                            ])
                            ->default('default')
                            ->required(),
                        CheckboxList::make('regions')
                            ->label('Regions')
                            ->options($availableRegions)
                            ->required()
                            ->columns(2)
                            ->helperText('Select which regions this layout should include'),
                        Toggle::make('is_default')
                            ->label('Set as Default Layout')
                            ->helperText('Only one default layout per business is allowed')
                            ->default(false),
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
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'default' => 'success',
                        'full-width' => 'info',
                        'landing' => 'warning',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('regions')
                    ->label('Regions')
                    ->formatStateUsing(function ($state): string {
                        if (is_string($state)) {
                            $state = json_decode($state, true) ?? [];
                        }
                        if (! is_array($state)) {
                            $state = [];
                        }

                        return count($state).' regions';
                    })
                    ->badge()
                    ->color('gray'),
                TextColumn::make('business.name')
                    ->label('Business')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('contents_count')
                    ->label('Used In')
                    ->counts('contents')
                    ->sortable(),
                TextColumn::make('compiled_at')
                    ->label('Compiled')
                    ->dateTime()
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
                        'default' => 'Default',
                        'full-width' => 'Full Width',
                        'landing' => 'Landing',
                        'page' => 'Page',
                    ]),
                TernaryFilter::make('is_default')
                    ->label('Default Layout')
                    ->placeholder('All')
                    ->trueLabel('Default only')
                    ->falseLabel('Non-default only'),
                SelectFilter::make('business_id')
                    ->label('Business')
                    ->relationship('business', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (Layout $record) {
                        // Prevent deletion if layout is used by content
                        if ($record->contents()->count() > 0) {
                            throw new \Exception('Cannot delete layout that is assigned to content. Remove assignments first.');
                        }
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if ($record->contents()->count() > 0) {
                                    throw new \Exception('Cannot delete layouts that are assigned to content. Remove assignments first.');
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
            'index' => Pages\ListLayouts::route('/'),
            'create' => Pages\CreateLayout::route('/create'),
            'edit' => Pages\EditLayout::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['business', 'contents']);

        // Scope by current business if available
        $business = \App\Domain\Businesses\Models\Business::active()->first();
        if ($business) {
            $query->forBusiness($business->id);
        }

        return $query;
    }
}
