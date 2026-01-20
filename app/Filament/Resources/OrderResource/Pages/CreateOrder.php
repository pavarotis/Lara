<?php

declare(strict_types=1);

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default values if not provided
        $data['status'] = $data['status'] ?? 'pending';
        $data['type'] = $data['type'] ?? 'pickup';
        $data['subtotal'] = $data['subtotal'] ?? 0;
        $data['tax'] = $data['tax'] ?? 0;
        $data['total'] = $data['total'] ?? 0;

        // Auto-generate order number if not provided
        if (empty($data['order_number'])) {
            $data['order_number'] = 'ORD-'.date('Ymd').'-'.strtoupper(substr(uniqid(), -6));
        }

        return $data;
    }
}
