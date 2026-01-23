<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domain\Marketing\Models\MailCampaign;
use App\Filament\Resources\MailCampaignResource\Pages;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MailCampaignResource extends Resource
{
    protected static ?string $model = MailCampaign::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Mail Campaigns';

    protected static ?string $modelLabel = 'Mail Campaign';

    protected static ?string $pluralModelLabel = 'Mail Campaigns';

    protected static string|\UnitEnum|null $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Campaign Information')
                    ->columns(2)
                    ->schema([
                        Select::make('business_id')
                            ->label('Business')
                            ->relationship('business', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->extraAttributes(['data-cy' => 'mail-campaign-business'])
                            ->default(fn () => \App\Domain\Businesses\Models\Business::active()->first()?->id)
                            ->helperText('The business this campaign belongs to'),
                        TextInput::make('name')
                            ->label('Campaign Name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Internal campaign name'),
                        TextInput::make('subject')
                            ->label('Subject')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Email subject line'),
                        Select::make('type')
                            ->label('Email Type')
                            ->options([
                                'plain' => 'Plain Text',
                                'html' => 'HTML',
                            ])
                            ->required()
                            ->default('html')
                            ->live()
                            ->helperText('Email format'),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'sending' => 'Sending',
                                'sent' => 'Sent',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('draft'),
                    ]),
                Section::make('Content')
                    ->schema([
                        CodeEditor::make('body')
                            ->label('Email Body')
                            ->required()
                            ->columnSpanFull()
                            ->language(fn ($get) => $get('type') === 'html' ? Language::Html : null)
                            ->extraAttributes(['data-cy' => 'mail-campaign-body'])
                            ->helperText('Email body content (HTML or plain text depending on type)'),
                    ]),
                Section::make('Scheduling')
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('scheduled_at')
                            ->label('Scheduled At')
                            ->nullable()
                            ->helperText('Schedule campaign send time'),
                        DateTimePicker::make('sent_at')
                            ->label('Sent At')
                            ->nullable()
                            ->disabled()
                            ->helperText('Actual send time (read-only)'),
                    ]),
                Section::make('Statistics')
                    ->columns(3)
                    ->schema([
                        TextInput::make('sent_count')
                            ->label('Sent')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->helperText('Emails sent'),
                        TextInput::make('opened_count')
                            ->label('Opened')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->helperText('Emails opened'),
                        TextInput::make('clicked_count')
                            ->label('Clicked')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->helperText('Links clicked'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Campaign Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('subject')
                    ->label('Subject')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'plain' => 'gray',
                        'html' => 'info',
                        default => 'gray',
                    })
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'scheduled' => 'warning',
                        'sending' => 'info',
                        'sent' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('sent_count')
                    ->label('Sent')
                    ->sortable()
                    ->badge()
                    ->color('success'),
                TextColumn::make('opened_count')
                    ->label('Opened')
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->toggleable(),
                TextColumn::make('clicked_count')
                    ->label('Clicked')
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->toggleable(),
                TextColumn::make('scheduled_at')
                    ->label('Scheduled')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('sent_at')
                    ->label('Sent At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('business.name')
                    ->label('Business')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'sending' => 'Sending',
                        'sent' => 'Sent',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'plain' => 'Plain Text',
                        'html' => 'HTML',
                    ]),
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
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['business']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMailCampaigns::route('/'),
            'create' => Pages\CreateMailCampaign::route('/create'),
            'edit' => Pages\EditMailCampaign::route('/{record}/edit'),
        ];
    }
}
