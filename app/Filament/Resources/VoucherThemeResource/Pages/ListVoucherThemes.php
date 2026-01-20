<?php

declare(strict_types=1);

namespace App\Filament\Resources\VoucherThemeResource\Pages;

use App\Filament\Resources\VoucherThemeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVoucherThemes extends ListRecords
{
    protected static string $resource = VoucherThemeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Create Voucher Theme')
                ->icon('heroicon-o-plus'),
        ];
    }
}
