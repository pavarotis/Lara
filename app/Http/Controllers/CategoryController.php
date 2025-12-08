<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Businesses\Models\Business;
use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Services\GetActiveProductsService;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        private GetActiveProductsService $productsService
    ) {}

    /**
     * Display products for a specific category
     */
    public function show(string $slug): View
    {
        $business = Business::active()->first();

        if (! $business) {
            abort(404, 'No active business found');
        }

        $category = Category::where('business_id', $business->id)
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        $products = $this->productsService->forCategory($category);

        return view('category', [
            'business' => $business,
            'category' => $category,
            'products' => $products,
        ]);
    }
}
