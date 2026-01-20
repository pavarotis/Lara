<?php

declare(strict_types=1);

namespace App\Filament\Resources\CouponResource\Pages;

use App\Filament\Resources\CouponResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCoupon extends CreateRecord
{
    protected static string $resource = CouponResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = $data['type'] ?? 'percentage';
        $data['discount'] = $data['discount'] ?? 0;
        $data['uses_per_customer'] = $data['uses_per_customer'] ?? 1;
        $data['uses_count'] = $data['uses_count'] ?? 0;
        $data['is_active'] = $data['is_active'] ?? true;

        return $data;
    }
}
