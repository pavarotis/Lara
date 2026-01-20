<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domain\Themes\Models\ThemePreset;
use App\Filament\Resources\ThemePresetResource\Pages;
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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ThemePresetResource extends Resource
{
    protected static ?string $model = ThemePreset::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $navigationLabel = 'Skins';

    protected static ?string $modelLabel = 'Skin';

    protected static ?string $pluralModelLabel = 'Skins';

    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        $headerVariants = collect(config('header_variants', []))->mapWithKeys(function ($config, $key) {
            return [$key => $config['name'] ?? $key];
        })->toArray();

        $footerVariants = collect(config('footer_variants', []))->mapWithKeys(function ($config, $key) {
            return [$key => $config['name'] ?? $key];
        })->toArray();

        return $schema
            ->components([
                Section::make('Basic Information')
                    ->components([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (! $state) {
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            }),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('URL-friendly identifier (auto-generated from name)'),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Only active skins can be assigned to businesses'),
                    ]),
                Section::make('Design Tokens')
                    ->components([
                        CodeEditor::make('tokens')
                            ->label('Design Tokens (JSON)')
                            ->required()
                            ->json()
                            ->helperText('Design tokens for colors, typography, spacing, etc. Example: {"colors":{"primary":"#3b82f6","accent":"#10b981"},"typography":{"fontFamily":"Inter"}}'),
                    ]),
                Section::make('Default Settings')
                    ->components([
                        Select::make('default_header_variant')
                            ->label('Default Header Variant')
                            ->options($headerVariants)
                            ->default('minimal')
                            ->required(),
                        Select::make('default_footer_variant')
                            ->label('Default Footer Variant')
                            ->options($footerVariants)
                            ->default('simple')
                            ->required(),
                        CodeEditor::make('default_modules')
                            ->label('Default Modules (JSON)')
                            ->json()
                            ->helperText('Default modules per region. Format: {"header_top": ["module1"], "main_content": ["module2"]}'),
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
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('businesses_count')
                    ->label('Used By')
                    ->counts('businesses')
                    ->sortable(),
                TextColumn::make('default_header_variant')
                    ->label('Header')
                    ->badge()
                    ->color('info'),
                TextColumn::make('default_footer_variant')
                    ->label('Footer')
                    ->badge()
                    ->color('info'),
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
                TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (ThemePreset $record) {
                        // Prevent deletion if preset is used by businesses
                        if ($record->businesses()->count() > 0) {
                            throw new \Exception('Cannot delete skin that is assigned to businesses. Remove assignments first.');
                        }
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if ($record->businesses()->count() > 0) {
                                    throw new \Exception('Cannot delete skins that are assigned to businesses. Remove assignments first.');
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
            'index' => Pages\ListThemePresets::route('/'),
            'create' => Pages\CreateThemePreset::route('/create'),
            'edit' => Pages\EditThemePreset::route('/{record}/edit'),
        ];
    }
}
