<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Services;

use App\Domain\Catalog\Models\Product;

class DeleteProductService
{
    public function execute(Product $product): bool
    {
        $business = $product->business;

        $deleted = $product->delete();

        // Clear menu cache
        if ($deleted) {
            app(GetMenuForBusinessService::class)->clearCache($business);
        }

        return $deleted;
    }
}
