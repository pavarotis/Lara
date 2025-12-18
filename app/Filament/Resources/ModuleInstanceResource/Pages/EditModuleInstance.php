<?php

namespace App\Filament\Resources\ModuleInstanceResource\Pages;

use App\Filament\Resources\ModuleInstanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditModuleInstance extends EditRecord
{
    protected static string $resource = ModuleInstanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($record) {
                    // Check if module is assigned to any content
                    if ($record->assignments()->count() > 0) {
                        throw new \Exception('Cannot delete module that is assigned to content. Remove assignments first.');
                    }
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
