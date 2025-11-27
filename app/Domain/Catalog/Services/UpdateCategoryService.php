<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Services;

use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Services\GetMenuForBusinessService;

class UpdateCategoryService
{
    public function execute(Category $category, array $data): Category
    {
        $category->update($data);

        // Clear menu cache
        app(GetMenuForBusinessService::class)->clearCache($category->business);

        return $category->fresh();
    }
}

