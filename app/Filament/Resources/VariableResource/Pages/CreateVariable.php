<?php

declare(strict_types=1);

namespace App\Filament\Resources\VariableResource\Pages;

use App\Filament\Resources\VariableResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVariable extends CreateRecord
{
    protected static string $resource = VariableResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Handle boolean value
        if (isset($data['type']) && $data['type'] === 'boolean') {
            $data['value'] = isset($data['value']) && $data['value'] ? '1' : '0';
        }

        // Handle JSON value
        if (isset($data['type']) && $data['type'] === 'json' && isset($data['value']) && is_string($data['value'])) {
            // Validate JSON
            $decoded = json_decode($data['value'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Illuminate\Validation\ValidationException(
                    validator([], []),
                    ['value' => ['Invalid JSON format']]
                );
            }
        }

        // Handle number value
        if (isset($data['type']) && $data['type'] === 'number' && isset($data['value'])) {
            $data['value'] = (string) $data['value'];
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
