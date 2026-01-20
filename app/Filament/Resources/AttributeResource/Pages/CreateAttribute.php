<?php

declare(strict_types=1);

namespace App\Filament\Resources\AttributeResource\Pages;

use App\Filament\Resources\AttributeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAttribute extends CreateRecord
{
    protected static string $resource = AttributeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default values if not provided
        $data['is_active'] = $data['is_active'] ?? true;
        $data['is_required'] = $data['is_required'] ?? false;
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['type'] = $data['type'] ?? 'text';

        // Parse options JSON if provided
        if (isset($data['options']) && is_string($data['options'])) {
            $data['options'] = json_decode($data['options'], true) ?? [];
        }

        return $data;
    }
}
