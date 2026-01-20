<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContentResource\Pages;

use App\Filament\Resources\ContentResource;
use App\Support\ContentStatusHelper;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateContent extends CreateRecord
{
    protected static string $resource = ContentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set defaults for blog post
        $data['type'] = 'article'; // Always article for blog posts
        $data['status'] = $data['status'] ?? ContentStatusHelper::draft();
        $data['body_json'] = $data['body_json'] ?? [];
        $data['meta'] = $data['meta'] ?? [];
        $data['created_by'] = auth()->id();

        // Auto-generate slug if not provided
        if (empty($data['slug']) && ! empty($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
