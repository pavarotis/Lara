<?php

declare(strict_types=1);

namespace App\Filament\Resources\MailCampaignResource\Pages;

use App\Filament\Resources\MailCampaignResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMailCampaign extends CreateRecord
{
    protected static string $resource = MailCampaignResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = $data['type'] ?? 'html';
        $data['status'] = $data['status'] ?? 'draft';
        $data['sent_count'] = $data['sent_count'] ?? 0;
        $data['opened_count'] = $data['opened_count'] ?? 0;
        $data['clicked_count'] = $data['clicked_count'] ?? 0;

        return $data;
    }
}
