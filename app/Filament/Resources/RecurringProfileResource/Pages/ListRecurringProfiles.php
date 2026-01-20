<?php

declare(strict_types=1);

namespace App\Filament\Resources\RecurringProfileResource\Pages;

use App\Filament\Resources\RecurringProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRecurringProfiles extends ListRecords
{
    protected static string $resource = RecurringProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Create Recurring Profile')
                ->icon('heroicon-o-plus'),
        ];
    }
}
