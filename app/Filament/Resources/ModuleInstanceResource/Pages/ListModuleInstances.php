<?php

namespace App\Filament\Resources\ModuleInstanceResource\Pages;

use App\Filament\Resources\ModuleInstanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListModuleInstances extends ListRecords
{
    protected static string $resource = ModuleInstanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
