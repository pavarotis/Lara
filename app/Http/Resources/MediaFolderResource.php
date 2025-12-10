<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\Media\Models\MediaFolder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * MediaFolder API Resource
 *
 * Provides consistent JSON format for MediaFolder API responses
 */
class MediaFolderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var MediaFolder $folder */
        $folder = $this->resource;

        return [
            'id' => $folder->id,
            'business_id' => $folder->business_id,
            'parent_id' => $folder->parent_id,
            'name' => $folder->name,
            'path' => $folder->path ?? $folder->getPath(), // Helper method from model
            'created_at' => $folder->created_at->toIso8601String(),
            'updated_at' => $folder->updated_at->toIso8601String(),
            'parent' => $folder->parent ? [
                'id' => $folder->parent->id,
                'name' => $folder->parent->name,
            ] : null,
            'children' => MediaFolderResource::collection($folder->children),
            'creator' => $folder->creator ? [
                'id' => $folder->creator->id,
                'name' => $folder->creator->name,
            ] : null,
        ];
    }
}
