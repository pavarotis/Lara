<?php

declare(strict_types=1);

namespace App\Filament\Resources\FilterValueResource\Pages;

use App\Filament\Resources\FilterValueResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFilterValues extends ListRecords
{
    protected static string $resource = FilterValueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Create Filter Value')
                ->icon('heroicon-o-plus'),
        ];
    }
}
