<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domain\Content\Models\BlogComment;
use App\Filament\Resources\BlogCommentResource\Pages;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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

class BlogCommentResource extends Resource
{
    protected static ?string $model = BlogComment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Post Comments';

    protected static ?string $modelLabel = 'Comment';

    protected static ?string $pluralModelLabel = 'Comments';

    protected static string|\UnitEnum|null $navigationGroup = 'CMS / Blog';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Comment Information')
                    ->components([
                        Select::make('content_id')
                            ->label('Blog Post')
                            ->relationship(
                                'content',
                                'title',
                                fn ($query) => $query->where('type', 'article')
                            )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('The blog post (article) this comment belongs to'),
                        Select::make('parent_id')
                            ->label('Reply To')
                            ->relationship('parent', 'body', fn ($query) => $query->limit(50))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Leave empty for top-level comment, or select a parent comment for replies'),
                    ]),
                Section::make('Author Information')
                    ->components([
                        TextInput::make('author_name')
                            ->label('Author Name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('author_email')
                            ->label('Author Email')
                            ->required()
                            ->email()
                            ->maxLength(255),
                    ]),
                Section::make('Comment Content')
                    ->components([
                        Textarea::make('body')
                            ->label('Comment Body')
                            ->required()
                            ->rows(5)
                            ->maxLength(5000)
                            ->helperText('The comment content'),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'spam' => 'Spam',
                                'rejected' => 'Rejected',
                            ])
                            ->default('pending')
                            ->required(),
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
                TextColumn::make('content.title')
                    ->label('Blog Post')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                TextColumn::make('author_name')
                    ->label('Author')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('body')
                    ->label('Comment')
                    ->limit(50)
                    ->searchable()
                    ->wrap(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'spam' => 'danger',
                        'rejected' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('parent_id')
                    ->label('Reply To')
                    ->formatStateUsing(fn (?int $state) => $state ? "Comment #{$state}" : '—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('replies_count')
                    ->label('Replies')
                    ->counts('replies')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'spam' => 'Spam',
                        'rejected' => 'Rejected',
                    ]),
                SelectFilter::make('content_id')
                    ->label('Blog Post')
                    ->relationship(
                        'content',
                        'title',
                        fn ($query) => $query->where('type', 'article')
                    )
                    ->searchable()
                    ->preload(),
                SelectFilter::make('parent_id')
                    ->label('Type')
                    ->options([
                        'top-level' => 'Top Level',
                        'replies' => 'Replies',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if ($data['value'] === 'top-level') {
                            return $query->whereNull('parent_id');
                        }
                        if ($data['value'] === 'replies') {
                            return $query->whereNotNull('parent_id');
                        }

                        return $query;
                    }),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (BlogComment $record) => ! $record->isApproved())
                    ->requiresConfirmation()
                    ->action(function (BlogComment $record) {
                        $record->approve();
                        Notification::make()
                            ->title('Comment approved')
                            ->success()
                            ->send();
                    }),
                Action::make('markAsSpam')
                    ->label('Mark as Spam')
                    ->icon('heroicon-o-shield-exclamation')
                    ->color('danger')
                    ->visible(fn (BlogComment $record) => ! $record->isSpam())
                    ->requiresConfirmation()
                    ->action(function (BlogComment $record) {
                        $record->markAsSpam();
                        Notification::make()
                            ->title('Comment marked as spam')
                            ->warning()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('gray')
                    ->visible(fn (BlogComment $record) => $record->isPending())
                    ->requiresConfirmation()
                    ->action(function (BlogComment $record) {
                        $record->reject();
                        Notification::make()
                            ->title('Comment rejected')
                            ->info()
                            ->send();
                    }),
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil'),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    Action::make('bulkApprove')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if (! $record->isApproved()) {
                                    $record->approve();
                                    $count++;
                                }
                            }
                            Notification::make()
                                ->title("{$count} comments approved")
                                ->success()
                                ->send();
                        }),
                    Action::make('bulkMarkAsSpam')
                        ->label('Mark as Spam')
                        ->icon('heroicon-o-shield-exclamation')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if (! $record->isSpam()) {
                                    $record->markAsSpam();
                                    $count++;
                                }
                            }
                            Notification::make()
                                ->title("{$count} comments marked as spam")
                                ->warning()
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
            'index' => Pages\ListBlogComments::route('/'),
            'create' => Pages\CreateBlogComment::route('/create'),
            'edit' => Pages\EditBlogComment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('content', fn ($query) => $query->where('type', 'article'))
            ->with(['content', 'parent', 'replies']);
    }
}
