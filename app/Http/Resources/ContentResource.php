<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\Content\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Content API Resource
 *
 * Provides consistent JSON format for Content API responses
 */
class ContentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Content $content */
        $content = $this->resource;

        return [
            'id' => $content->id,
            'business_id' => $content->business_id,
            'type' => $content->type,
            'slug' => $content->slug,
            'title' => $content->title,
            'body' => $content->body_json, // Blocks array
            'meta' => $content->meta,
            'status' => $content->status,
            'published_at' => $content->published_at?->toIso8601String(),
            'created_at' => $content->created_at->toIso8601String(),
            'updated_at' => $content->updated_at->toIso8601String(),
            'creator' => $content->creator ? [
                'id' => $content->creator->id,
                'name' => $content->creator->name,
            ] : null,
        ];
    }
}
