<?php

declare(strict_types=1);

namespace App\Filament\Resources\CustomFieldResource\Pages;

use App\Filament\Resources\CustomFieldResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomField extends EditRecord
{
    protected static string $resource = CustomFieldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Delete')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Convert options array to JSON string for CodeEditor
        if (isset($data['options']) && is_array($data['options'])) {
            $data['options'] = json_encode($data['options'], JSON_PRETTY_PRINT);
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Parse options JSON if provided
        if (! empty($data['options']) && is_string($data['options'])) {
            $decoded = json_decode($data['options'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data['options'] = $decoded;
            } else {
                $data['options'] = null;
            }
        }

        return $data;
    }
}
