<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected ?array $attributesData = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Delete')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Handle product attributes
        if (isset($data['productAttributes']) && is_array($data['productAttributes'])) {
            $attributesData = $data['productAttributes'];
            unset($data['productAttributes']);

            // Store for afterSave
            $this->attributesData = $attributesData;
        }

        return $data;
    }

    protected function afterSave(): void
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
