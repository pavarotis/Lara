<?php

declare(strict_types=1);

namespace App\Filament\Resources\AttributeResource\Pages;

use App\Filament\Resources\AttributeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttribute extends EditRecord
{
    protected static string $resource = AttributeResource::class;

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
        if (isset($data['options']) && is_string($data['options'])) {
            $data['options'] = json_decode($data['options'], true) ?? [];
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Delete')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation(),
        ];
    }
}
