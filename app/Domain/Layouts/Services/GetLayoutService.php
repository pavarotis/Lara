<?php

declare(strict_types=1);

namespace App\Domain\Layouts\Services;

use App\Domain\Layouts\Models\Layout;

class GetLayoutService
{
    /**
     * Get layout for business (by ID or default)
     */
    public function forBusiness(int $businessId, ?int $layoutId = null): ?Layout
    {
        if ($layoutId) {
            return Layout::forBusiness($businessId)
                ->find($layoutId);
        }

        return $this->defaultForBusiness($businessId);
    }

    /**
     * Get default layout for business
     */
    public function defaultForBusiness(int $businessId): ?Layout
    {
        return Layout::forBusiness($businessId)
            ->default()
            ->with('business')
            ->first();
    }

    /**
     * Get layout with regions loaded
     */
    public function withRegions(int $layoutId): Layout
    {
        return Layout::with('business')->findOrFail($layoutId);
    }
}
