<?php

declare(strict_types=1);

namespace App\Filament\Resources\CustomerApprovalResource\Pages;

use App\Filament\Resources\CustomerApprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomerApproval extends EditRecord
{
    protected static string $resource = CustomerApprovalResource::class;

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
