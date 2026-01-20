<?php

declare(strict_types=1);

namespace App\Filament\Resources\CustomerApprovalResource\Pages;

use App\Filament\Resources\CustomerApprovalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerApproval extends CreateRecord
{
    protected static string $resource = CustomerApprovalResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = $data['status'] ?? 'pending';

        return $data;
    }
}
