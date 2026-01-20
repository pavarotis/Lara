<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domain\Catalog\Models\RecurringProfile;
use App\Filament\Resources\RecurringProfileResource\Pages;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
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

class RecurringProfileResource extends Resource
{
    protected static ?string $model = RecurringProfile::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationLabel = 'Recurring Profiles';

    protected static ?string $modelLabel = 'Recurring Profile';

    protected static ?string $pluralModelLabel = 'Recurring Profiles';

    protected static string|\UnitEnum|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 2;

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
                            ->default(fn () => \App\Domain\Businesses\Models\Business::active()->first()?->id)
                            ->helperText('The business this profile belongs to'),
                        Select::make('customer_id')
                            ->label('Customer')
                            ->relationship('customer', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('The customer who has this subscription'),
                        Select::make('product_id')
                            ->label('Product')
                            ->relationship('product', 'name', fn (Builder $query) => $query->where('is_available', true))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('The product for this subscription'),
                        TextInput::make('name')
                            ->label('Profile Name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('A descriptive name for this subscription profile'),
                    ]),
                Section::make('Subscription Details')
                    ->columns(2)
                    ->schema([
                        Select::make('frequency')
                            ->label('Frequency')
                            ->options([
                                'daily' => 'Daily',
                                'weekly' => 'Weekly',
                                'monthly' => 'Monthly',
                                'yearly' => 'Yearly',
                            ])
                            ->required()
                            ->default('monthly')
                            ->helperText('How often the customer is billed'),
                        TextInput::make('duration')
                            ->label('Duration (Cycles)')
                            ->numeric()
                            ->minValue(1)
                            ->nullable()
                            ->helperText('Number of billing cycles (leave empty for ongoing)'),
                        TextInput::make('price')
                            ->label('Price')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01)
                            ->minValue(0)
                            ->required()
                            ->helperText('Price per billing cycle'),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Active',
                                'paused' => 'Paused',
                                'cancelled' => 'Cancelled',
                                'completed' => 'Completed',
                            ])
                            ->required()
                            ->default('active')
                            ->helperText('Current status of the subscription'),
                    ]),
                Section::make('Billing Information')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('next_billing_date')
                            ->label('Next Billing Date')
                            ->nullable()
                            ->helperText('When the next billing will occur'),
                        DatePicker::make('last_billing_date')
                            ->label('Last Billing Date')
                            ->nullable()
                            ->helperText('When the last billing occurred'),
                        TextInput::make('total_cycles')
                            ->label('Total Cycles')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->disabled()
                            ->helperText('Number of times billed (auto-calculated)'),
                    ]),
                Section::make('Notes')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->maxLength(65535)
                            ->columnSpanFull()
                            ->helperText('Additional notes about this subscription'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Profile Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success'),
                TextColumn::make('frequency')
                    ->label('Frequency')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'monthly' => 'Monthly',
                        'yearly' => 'Yearly',
                        default => $state,
                    }),
                TextColumn::make('price')
                    ->label('Price')
                    ->money('EUR')
                    ->sortable()
                    ->alignEnd(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'paused' => 'warning',
                        'cancelled' => 'danger',
                        'completed' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('next_billing_date')
                    ->label('Next Billing')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->next_billing_date && $record->next_billing_date->isPast() ? 'danger' : null),
                TextColumn::make('total_cycles')
                    ->label('Cycles')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('info'),
                TextColumn::make('duration')
                    ->label('Duration')
                    ->formatStateUsing(fn ($state) => $state ? "{$state} cycles" : 'Ongoing')
                    ->badge()
                    ->color('gray'),
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
                SelectFilter::make('business_id')
                    ->label('Business')
                    ->relationship('business', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'paused' => 'Paused',
                        'cancelled' => 'Cancelled',
                        'completed' => 'Completed',
                    ]),
                SelectFilter::make('frequency')
                    ->label('Frequency')
                    ->options([
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'monthly' => 'Monthly',
                        'yearly' => 'Yearly',
                    ]),
            ])
            ->actions([
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil'),
                Action::make('pause')
                    ->label('Pause')
                    ->icon('heroicon-o-pause')
                    ->color('warning')
                    ->visible(fn (RecurringProfile $record) => $record->isActive())
                    ->requiresConfirmation()
                    ->action(function (RecurringProfile $record) {
                        $record->update(['status' => 'paused']);
                        Notification::make()
                            ->title('Subscription paused')
                            ->success()
                            ->send();
                    }),
                Action::make('resume')
                    ->label('Resume')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->visible(fn (RecurringProfile $record) => $record->isPaused())
                    ->requiresConfirmation()
                    ->action(function (RecurringProfile $record) {
                        $record->update(['status' => 'active']);
                        Notification::make()
                            ->title('Subscription resumed')
                            ->success()
                            ->send();
                    }),
                Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (RecurringProfile $record) => ! $record->isCancelled() && ! $record->isCompleted())
                    ->requiresConfirmation()
                    ->action(function (RecurringProfile $record) {
                        $record->update(['status' => 'cancelled']);
                        Notification::make()
                            ->title('Subscription cancelled')
                            ->warning()
                            ->send();
                    }),
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
            ->with(['business', 'customer', 'product']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecurringProfiles::route('/'),
            'create' => Pages\CreateRecurringProfile::route('/create'),
            'edit' => Pages\EditRecurringProfile::route('/{record}/edit'),
        ];
    }
}
