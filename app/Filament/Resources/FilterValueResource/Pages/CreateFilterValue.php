<?php

declare(strict_types=1);

namespace App\Filament\Resources\FilterValueResource\Pages;

use App\Filament\Resources\FilterValueResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFilterValue extends CreateRecord
{
    protected static string $resource = FilterValueResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default values if not provided
        $data['is_active'] = $data['is_active'] ?? true;
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }
}
