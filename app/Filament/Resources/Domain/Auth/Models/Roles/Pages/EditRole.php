<?php

namespace App\Filament\Resources\Domain\Auth\Models\Roles\Pages;

use App\Filament\Resources\Domain\Auth\Models\Roles\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
