<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domain\Content\Models\Content;
use App\Filament\Resources\ContentResource\Pages;
use App\Support\ContentStatusHelper;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ContentResource extends Resource
{
    protected static ?string $model = Content::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Posts';

    protected static ?string $modelLabel = 'Blog Post';

    protected static ?string $pluralModelLabel = 'Blog Posts';

    protected static string|\UnitEnum|null $navigationGroup = 'CMS / Blog';

    protected static ?int $navigationSort = 1;

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
                            ->default(function () {
                                return \App\Domain\Businesses\Models\Business::active()->first()?->id;
                            }),
                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('slug', Str::slug($state));
                            })
                            ->helperText('The blog post title'),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('URL-friendly identifier (auto-generated from title)'),
                        Select::make('layout_id')
                            ->label('Layout')
                            ->relationship('layout', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Optional layout for this blog post'),
                        CheckboxList::make('categories')
                            ->label('Categories')
                            ->relationship('categories', 'name')
                            ->searchable()
                            ->columns(2)
                            ->helperText('Select categories for this blog post'),
                    ]),
                Section::make('Content')
                    ->description('Use the content editor to create and edit blog post content visually.')
                    ->schema([
                        Textarea::make('body_json_preview')
                            ->label('Content Preview')
                            ->helperText('Block-based content. Use the dedicated content editor for full editing.')
                            ->rows(5)
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(function ($record) {
                                if (! $record) {
                                    return 'Content will be created using the visual editor.';
                                }
                                $blocks = is_array($record->body_json) ? $record->body_json : [];

                                return count($blocks).' content blocks';
                            }),
                    ]),
                Section::make('Publishing')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                ContentStatusHelper::draft() => 'Draft',
                                ContentStatusHelper::published() => 'Published',
                                ContentStatusHelper::archived() => 'Archived',
                            ])
                            ->default(ContentStatusHelper::draft())
                            ->required(),
                        DateTimePicker::make('published_at')
                            ->label('Publish Date')
                            ->nullable()
                            ->helperText('When to publish this post (leave empty for immediate)'),
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
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        ContentStatusHelper::published() => 'success',
                        ContentStatusHelper::draft() => 'warning',
                        ContentStatusHelper::archived() => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('business.name')
                    ->label('Business')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('categories.name')
                    ->label('Categories')
                    ->badge()
                    ->separator(',')
                    ->limit(3)
                    ->toggleable(),
                TextColumn::make('comments_count')
                    ->label('Comments')
                    ->counts('allComments')
                    ->sortable(),
                TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        ContentStatusHelper::draft() => 'Draft',
                        ContentStatusHelper::published() => 'Published',
                        ContentStatusHelper::archived() => 'Archived',
                    ]),
                SelectFilter::make('business_id')
                    ->label('Business')
                    ->relationship('business', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Action::make('publish')
                    ->label('Publish')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Content $record) => ! $record->isPublished())
                    ->requiresConfirmation()
                    ->action(function (Content $record) {
                        $record->publish();
                        Notification::make()
                            ->title('Blog post published')
                            ->success()
                            ->send();
                    }),
                Action::make('archive')
                    ->label('Archive')
                    ->icon('heroicon-o-archive-box')
                    ->color('gray')
                    ->visible(fn (Content $record) => $record->isPublished())
                    ->requiresConfirmation()
                    ->action(function (Content $record) {
                        $record->archive();
                        Notification::make()
                            ->title('Blog post archived')
                            ->info()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    Action::make('bulkPublish')
                        ->label('Publish Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if (! $record->isPublished()) {
                                    $record->publish();
                                    $count++;
                                }
                            }
                            Notification::make()
                                ->title("{$count} blog posts published")
                                ->success()
                                ->send();
                        }),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListContents::route('/'),
            'create' => Pages\CreateContent::route('/create'),
            'edit' => Pages\EditContent::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('type', 'article') // Only blog posts (articles)
            ->with(['business', 'layout', 'creator', 'categories']);
    }
}
