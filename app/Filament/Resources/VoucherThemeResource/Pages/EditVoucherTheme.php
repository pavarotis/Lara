<?php

declare(strict_types=1);

namespace App\Filament\Resources\VoucherThemeResource\Pages;

use App\Filament\Resources\VoucherThemeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVoucherTheme extends EditRecord
{
    protected static string $resource = VoucherThemeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Delete')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->before(function ($record) {
                    if ($record->giftVouchers()->count() > 0) {
                        throw new \Exception('Cannot delete theme with vouchers. Remove voucher assignments first.');
                    }
                }),
        ];
    }
}
