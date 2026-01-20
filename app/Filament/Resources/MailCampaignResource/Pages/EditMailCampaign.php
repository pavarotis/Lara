<?php

declare(strict_types=1);

namespace App\Filament\Resources\MailCampaignResource\Pages;

use App\Filament\Resources\MailCampaignResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMailCampaign extends EditRecord
{
    protected static string $resource = MailCampaignResource::class;

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
