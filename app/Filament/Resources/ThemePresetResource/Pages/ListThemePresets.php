<?php

declare(strict_types=1);

namespace App\Filament\Resources\ThemePresetResource\Pages;

use App\Filament\Resources\ThemePresetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListThemePresets extends ListRecords
{
    protected static string $resource = ThemePresetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
