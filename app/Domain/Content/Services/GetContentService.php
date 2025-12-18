<?php

declare(strict_types=1);

namespace App\Domain\Content\Services;

use App\Domain\Content\Models\Content;
use Illuminate\Database\Eloquent\Collection;

class GetContentService
{
    /**
     * Get published content by slug for a business
     */
    public function bySlug(int $businessId, string $slug): ?Content
    {
        return Content::where('business_id', $businessId)
            ->where('slug', $slug)
            ->published()
            ->with(['business', 'layout.business']) // Eager load business and layout.business for theme resolution
            ->first();
    }

    /**
     * Get all published content of a specific type for a business
     */
    public function byType(int $businessId, string $type): Collection
    {
        return Content::where('business_id', $businessId)
            ->where('type', $type)
            ->published()
            ->get();
    }

    /**
     * Get content with revision history
     */
    public function withRevisions(int $contentId): ?Content
    {
        return Content::with('revisions.user')
            ->find($contentId);
    }
}
