<?php

namespace App\Filament\Resources\Domain\Auth\Models\Roles\Pages;

use App\Filament\Resources\Domain\Auth\Models\Roles\RoleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
