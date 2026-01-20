<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProductExtraResource\Pages;

use App\Filament\Resources\ProductExtraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductExtras extends ListRecords
{
    protected static string $resource = ProductExtraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
