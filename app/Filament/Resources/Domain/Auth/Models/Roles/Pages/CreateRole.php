<?php

namespace App\Filament\Resources\Domain\Auth\Models\Roles\Pages;

use App\Filament\Resources\Domain\Auth\Models\Roles\RoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;
}
