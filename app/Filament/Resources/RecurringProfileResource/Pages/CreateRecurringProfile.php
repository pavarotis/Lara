<?php

declare(strict_types=1);

namespace App\Filament\Resources\RecurringProfileResource\Pages;

use App\Filament\Resources\RecurringProfileResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRecurringProfile extends CreateRecord
{
    protected static string $resource = RecurringProfileResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default values if not provided
        $data['status'] = $data['status'] ?? 'active';
        $data['frequency'] = $data['frequency'] ?? 'monthly';
        $data['total_cycles'] = $data['total_cycles'] ?? 0;

        // Calculate next billing date based on frequency
        if (empty($data['next_billing_date'])) {
            $data['next_billing_date'] = match ($data['frequency']) {
                'daily' => now()->addDay(),
                'weekly' => now()->addWeek(),
                'monthly' => now()->addMonth(),
                'yearly' => now()->addYear(),
                default => now()->addMonth(),
            };
        }

        return $data;
    }
}
