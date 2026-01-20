<?php

declare(strict_types=1);

namespace App\Filament\Resources\ReturnResource\Pages;

use App\Filament\Resources\ReturnResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReturn extends CreateRecord
{
    protected static string $resource = ReturnResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default values if not provided
        $data['status'] = $data['status'] ?? 'pending';
        $data['return_date'] = $data['return_date'] ?? now();

        // Auto-generate return number if not provided
        if (empty($data['return_number'])) {
            $data['return_number'] = 'RET-'.date('Ymd').'-'.strtoupper(substr(uniqid(), -6));
        }

        return $data;
    }
}
