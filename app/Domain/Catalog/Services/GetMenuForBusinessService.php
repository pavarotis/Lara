<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Services;

use App\Domain\Businesses\Models\Business;
use App\Domain\Catalog\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class GetMenuForBusinessService
{
    /**
     * Get full menu (categories with products) for a business
     */
    public function execute(Business $business): Collection
    {
        $cacheKey = "menu_business_{$business->id}";

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($business) {
            return Category::where('business_id', $business->id)
                ->active()
                ->ordered()
                ->with(['products' => function ($query) {
                    $query->available()->ordered();
                }])
                ->get();
        });
    }

    /**
     * Clear cached menu for a business
     */
    public function clearCache(Business $business): void
    {
        Cache::forget("menu_business_{$business->id}");
    }
}
