<?php

declare(strict_types=1);

namespace App\Domain\Content\Services;

use App\Domain\Content\Models\Content;

/**
 * RenderContentService
 *
 * Skeleton/placeholder for Sprint 1
 * Full implementation will be in Sprint 3
 */
class RenderContentService
{
    /**
     * Render content blocks to HTML
     *
     * This is a placeholder implementation.
     * Full block rendering with theme support will be implemented in Sprint 3.
     */
    public function execute(Content $content): string
    {
        // Placeholder: Return empty string for now
        // Full implementation in Sprint 3 will:
        // - Resolve theme from business
        // - Render each block using theme views
        // - Fallback to default theme if block view not found
        return '';
    }
}
