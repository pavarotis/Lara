<?php

declare(strict_types=1);

namespace App\Filament\Resources\FilterValueResource\Pages;

use App\Filament\Resources\FilterValueResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFilterValue extends EditRecord
{
    protected static string $resource = FilterValueResource::class;

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
