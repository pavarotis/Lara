<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Services;

use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Services\GetMenuForBusinessService;

class UpdateProductService
{
    public function execute(Product $product, array $data): Product
    {
        $product->update($data);

        // Clear menu cache for this business
        app(GetMenuForBusinessService::class)->clearCache($product->business);

        return $product->fresh();
    }
}

