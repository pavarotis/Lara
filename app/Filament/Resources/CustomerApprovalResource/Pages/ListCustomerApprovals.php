<?php

declare(strict_types=1);

namespace App\Filament\Resources\CustomerApprovalResource\Pages;

use App\Filament\Resources\CustomerApprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomerApprovals extends ListRecords
{
    protected static string $resource = CustomerApprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Create Approval')
                ->icon('heroicon-o-plus'),
        ];
    }
}
