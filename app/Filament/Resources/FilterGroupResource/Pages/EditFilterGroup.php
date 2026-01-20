<?php

declare(strict_types=1);

namespace App\Filament\Resources\FilterGroupResource\Pages;

use App\Filament\Resources\FilterGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFilterGroup extends EditRecord
{
    protected static string $resource = FilterGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Delete')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->before(function ($record) {
                    if ($record->values()->count() > 0) {
                        throw new \Exception('Cannot delete filter group with values. Remove values first.');
                    }
                }),
        ];
    }
}
