<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected ?array $attributesData = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default values if not provided
        $data['is_available'] = $data['is_available'] ?? true;
        $data['is_featured'] = $data['is_featured'] ?? false;
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['price'] = $data['price'] ?? 0;

        // Handle product attributes
        if (isset($data['productAttributes']) && is_array($data['productAttributes'])) {
            $this->attributesData = $data['productAttributes'];
            unset($data['productAttributes']);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        // Sync product attributes
        if (isset($this->attributesData)) {
            $syncData = [];
            foreach ($this->attributesData as $attr) {
                if (! empty($attr['attribute_id']) && ! empty($attr['value'])) {
                    $syncData[$attr['attribute_id']] = ['value' => $attr['value']];
                }
            }
            $this->record->attributes()->sync($syncData);
        }
    }
}
