<?php

declare(strict_types=1);

namespace App\Filament\Resources\RecurringProfileResource\Pages;

use App\Filament\Resources\RecurringProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecurringProfile extends EditRecord
{
    protected static string $resource = RecurringProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Delete')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation(),
        ];
    }
}
