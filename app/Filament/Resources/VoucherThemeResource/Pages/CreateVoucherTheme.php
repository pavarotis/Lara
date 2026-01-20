<?php

declare(strict_types=1);

namespace App\Filament\Resources\VoucherThemeResource\Pages;

use App\Filament\Resources\VoucherThemeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVoucherTheme extends CreateRecord
{
    protected static string $resource = VoucherThemeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default values if not provided
        $data['is_active'] = $data['is_active'] ?? true;
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }
}
