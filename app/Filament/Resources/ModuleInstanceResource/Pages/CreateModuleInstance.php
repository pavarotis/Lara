<?php

namespace App\Filament\Resources\ModuleInstanceResource\Pages;

use App\Filament\Resources\ModuleInstanceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateModuleInstance extends CreateRecord
{
    protected static string $resource = ModuleInstanceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure business_id is set to current active business
        if (! isset($data['business_id'])) {
            $business = \App\Domain\Businesses\Models\Business::active()->first();
            if ($business) {
                $data['business_id'] = $business->id;
            }
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
