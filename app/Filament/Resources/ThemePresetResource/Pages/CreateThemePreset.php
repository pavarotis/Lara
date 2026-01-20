<?php

declare(strict_types=1);

namespace App\Filament\Resources\ThemePresetResource\Pages;

use App\Filament\Resources\ThemePresetResource;
use Filament\Resources\Pages\CreateRecord;

class CreateThemePreset extends CreateRecord
{
    protected static string $resource = ThemePresetResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default tokens if empty
        if (empty($data['tokens'])) {
            $data['tokens'] = [
                'colors' => [
                    'primary' => '#3b82f6',
                    'accent' => '#10b981',
                    'background' => '#ffffff',
                    'text' => '#1f2937',
                ],
                'typography' => [
                    'fontFamily' => 'Inter',
                    'fontSize' => ['base' => '16px'],
                ],
                'spacing' => ['unit' => '4px'],
            ];
        } elseif (is_string($data['tokens'])) {
            $data['tokens'] = json_decode($data['tokens'], true) ?? [];
        }

        // Ensure default_modules is valid JSON
        if (empty($data['default_modules'])) {
            $data['default_modules'] = [];
        } elseif (is_string($data['default_modules'])) {
            $data['default_modules'] = json_decode($data['default_modules'], true) ?? [];
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
