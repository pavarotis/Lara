<?php

declare(strict_types=1);

namespace App\Domain\Media\Services;

use App\Domain\Media\Models\Media;
use Illuminate\Database\Eloquent\Collection;

class GetMediaService
{
    /**
     * Get all media for a business
     */
    public function byBusiness(int $businessId): Collection
    {
        return Media::ofBusiness($businessId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get media in a specific folder
     */
    public function byFolder(int $folderId): Collection
    {
        return Media::inFolder($folderId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Search media by name
     */
    public function search(int $businessId, string $query): Collection
    {
        return Media::ofBusiness($businessId)
            ->search($query)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get media by type
     */
    public function byType(int $businessId, string $type): Collection
    {
        return Media::ofBusiness($businessId)
            ->ofType($type)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
