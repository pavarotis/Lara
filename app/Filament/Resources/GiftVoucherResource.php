<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domain\Sales\Models\GiftVoucher;
use App\Filament\Resources\GiftVoucherResource\Pages;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GiftVoucherResource extends Resource
{
    protected static ?string $model = GiftVoucher::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationLabel = 'Gift Vouchers';

    protected static ?string $modelLabel = 'Gift Voucher';

    protected static ?string $pluralModelLabel = 'Gift Vouchers';

    protected static string|\UnitEnum|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Voucher Information')
                    ->columns(2)
                    ->schema([
                        Select::make('business_id')
                            ->label('Business')
                            ->relationship('business', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->default(fn () => \App\Domain\Businesses\Models\Business::active()->first()?->id)
                            ->helperText('The business this voucher belongs to'),
                        Select::make('voucher_theme_id')
                            ->label('Voucher Theme')
                            ->relationship('voucherTheme', 'name', fn (Builder $query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Voucher theme/design'),
                        Select::make('order_id')
                            ->label('Order')
                            ->relationship('order', 'order_number')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Order that purchased this voucher'),
                        TextInput::make('code')
                            ->label('Voucher Code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Unique voucher code'),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'active' => 'Active',
                                'used' => 'Used',
                                'expired' => 'Expired',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('pending'),
                    ]),
                Section::make('Recipient Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('from_name')
                            ->label('From Name')
                            ->maxLength(255)
                            ->helperText('Sender name'),
                        TextInput::make('from_email')
                            ->label('From Email')
                            ->email()
                            ->maxLength(255)
                            ->helperText('Sender email'),
                        TextInput::make('to_name')
                            ->label('To Name')
                            ->maxLength(255)
                            ->helperText('Recipient name'),
                        TextInput::make('to_email')
                            ->label('To Email')
                            ->email()
                            ->maxLength(255)
                            ->helperText('Recipient email'),
                    ]),
                Section::make('Voucher Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->prefix('€')
                            ->required()
                            ->default(0)
                            ->helperText('Voucher amount'),
                        TextInput::make('balance')
                            ->label('Balance')
                            ->numeric()
                            ->prefix('€')
                            ->required()
                            ->default(0)
                            ->helperText('Remaining balance'),
                        DatePicker::make('expiry_date')
                            ->label('Expiry Date')
                            ->nullable()
                            ->helperText('Voucher expiry date'),
                        DatePicker::make('used_date')
                            ->label('Used Date')
                            ->nullable()
                            ->helperText('Date voucher was used'),
                    ]),
                Section::make('Message')
                    ->schema([
                        Textarea::make('message')
                            ->label('Message')
                            ->rows(4)
                            ->maxLength(65535)
                            ->columnSpanFull()
                            ->helperText('Personal message for recipient'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->copyable(),
                TextColumn::make('to_name')
                    ->label('Recipient')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('EUR')
                    ->sortable()
                    ->alignEnd(),
                TextColumn::make('balance')
                    ->label('Balance')
                    ->money('EUR')
                    ->sortable()
                    ->alignEnd()
                    ->color(fn ($record) => $record->balance > 0 ? 'success' : 'gray'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'active' => 'success',
                        'used' => 'info',
                        'expired' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('voucherTheme.name')
                    ->label('Theme')
                    ->badge()
                    ->color('info')
                    ->toggleable(),
                TextColumn::make('expiry_date')
                    ->label('Expiry Date')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('used_date')
                    ->label('Used Date')
                    ->date()
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
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'used' => 'Used',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('voucher_theme_id')
                    ->label('Theme')
                    ->relationship('voucherTheme', 'name')
                    ->searchable()
                    ->preload(),
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
            ->with(['business', 'voucherTheme', 'order']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGiftVouchers::route('/'),
            'create' => Pages\CreateGiftVoucher::route('/create'),
            'edit' => Pages\EditGiftVoucher::route('/{record}/edit'),
        ];
    }
}
