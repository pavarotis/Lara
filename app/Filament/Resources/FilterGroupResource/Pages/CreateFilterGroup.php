<?php

declare(strict_types=1);

namespace App\Filament\Resources\FilterGroupResource\Pages;

use App\Filament\Resources\FilterGroupResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFilterGroup extends CreateRecord
{
    protected static string $resource = FilterGroupResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default values if not provided
        $data['is_active'] = $data['is_active'] ?? true;
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }
}
