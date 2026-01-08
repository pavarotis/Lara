<?php

declare(strict_types=1);

namespace App\Filament\Resources\ApiKeyResource\Pages;

use App\Filament\Resources\ApiKeyResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateApiKey extends CreateRecord
{
    protected static string $resource = ApiKeyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generate key and secret if not provided
        if (empty($data['key'])) {
            $data['key'] = Str::random(32);
        }
        if (empty($data['secret'])) {
            $data['secret'] = Str::random(64);
        }

        return $data;
    }
}
