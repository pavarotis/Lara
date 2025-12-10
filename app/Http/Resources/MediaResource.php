<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\Media\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Media API Resource
 *
 * Provides consistent JSON format for Media API responses
 */
class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Media $media */
        $media = $this->resource;

        return [
            'id' => $media->id,
            'business_id' => $media->business_id,
            'folder_id' => $media->folder_id,
            'name' => $media->name,
            'path' => $media->path,
            'url' => $media->url ?? null, // Accessor from model
            'thumbnail_url' => $media->thumbnail_url ?? null, // Accessor from model
            'type' => $media->type,
            'mime' => $media->mime,
            'size' => $media->size,
            'metadata' => $media->metadata,
            'created_at' => $media->created_at->toIso8601String(),
            'updated_at' => $media->updated_at->toIso8601String(),
            'folder' => $media->folder ? [
                'id' => $media->folder->id,
                'name' => $media->folder->name,
                'path' => $media->folder->path,
            ] : null,
            'creator' => $media->creator ? [
                'id' => $media->creator->id,
                'name' => $media->creator->name,
            ] : null,
        ];
    }
}
