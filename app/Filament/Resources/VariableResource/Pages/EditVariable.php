<?php

declare(strict_types=1);

namespace App\Filament\Resources\VariableResource\Pages;

use App\Filament\Resources\VariableResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVariable extends EditRecord
{
    protected static string $resource = VariableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Convert value based on type for form display
        if (isset($data['type']) && isset($data['value'])) {
            if ($data['type'] === 'boolean') {
                $data['value'] = (bool) $data['value'];
            } elseif ($data['type'] === 'json' && is_string($data['value'])) {
                // Keep as JSON string for CodeEditor
                $decoded = json_decode($data['value'], true);
                if ($decoded !== null) {
                    $data['value'] = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                }
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
