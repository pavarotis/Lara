<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Services;

use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Services\GetMenuForBusinessService;

class DeleteCategoryService
{
    public function execute(Category $category): bool
    {
        $business = $category->business;

        // Note: Products will be cascade deleted due to FK constraint
        $deleted = $category->delete();

        if ($deleted) {
            app(GetMenuForBusinessService::class)->clearCache($business);
        }

        return $deleted;
    }
}

