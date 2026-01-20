<?php

declare(strict_types=1);

namespace App\Filament\Resources\LayoutResource\Pages;

use App\Domain\Layouts\Models\Layout;
use App\Filament\Resources\LayoutResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLayout extends CreateRecord
{
    protected static string $resource = LayoutResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure regions is an array
        if (isset($data['regions']) && ! is_array($data['regions'])) {
            $data['regions'] = [];
        }

        // If setting as default, unset other defaults for this business
        if (isset($data['is_default']) && $data['is_default']) {
            Layout::forBusiness($data['business_id'])
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
