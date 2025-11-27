<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Services;

use App\Domain\Businesses\Models\Business;
use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class GetActiveProductsService
{
    /**
     * Get all active products for a business
     */
    public function forBusiness(Business $business): Collection
    {
        return Product::where('business_id', $business->id)
            ->available()
            ->ordered()
            ->with('category')
            ->get();
    }

    /**
     * Get active products for a specific category
     */
    public function forCategory(Category $category): Collection
    {
        return Product::where('category_id', $category->id)
            ->available()
            ->ordered()
            ->get();
    }

    /**
     * Get featured products for a business
     */
    public function featuredForBusiness(Business $business): Collection
    {
        return Product::where('business_id', $business->id)
            ->available()
            ->featured()
            ->ordered()
            ->get();
    }
}

