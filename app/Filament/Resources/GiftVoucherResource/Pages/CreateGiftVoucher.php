<?php

declare(strict_types=1);

namespace App\Filament\Resources\GiftVoucherResource\Pages;

use App\Filament\Resources\GiftVoucherResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGiftVoucher extends CreateRecord
{
    protected static string $resource = GiftVoucherResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default values if not provided
        $data['status'] = $data['status'] ?? 'pending';
        $data['amount'] = $data['amount'] ?? 0;
        $data['balance'] = $data['balance'] ?? $data['amount'] ?? 0;

        // Auto-generate voucher code if not provided
        if (empty($data['code'])) {
            $data['code'] = 'VOUCHER-'.strtoupper(substr(md5(uniqid()), 0, 10));
        }

        return $data;
    }
}
