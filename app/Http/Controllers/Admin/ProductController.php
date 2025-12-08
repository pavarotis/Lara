<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Businesses\Models\Business;
use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Services\ImageUploadService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\StoreProductRequest;
use App\Http\Requests\Catalog\UpdateProductRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private ImageUploadService $imageService
    ) {}

    /**
     * Display a listing of products
     */
    public function index(Request $request): View
    {
        $business = Business::active()->first();

        $products = Product::where('business_id', $business->id)
            ->with('category')
            ->orderBy('sort_order')
            ->paginate(15);

        return view('admin.products.index', [
            'products' => $products,
            'business' => $business,
        ]);
    }

    /**
     * Show the form for creating a new product
     */
    public function create(): View
    {
        $business = Business::active()->first();
        $categories = Category::where('business_id', $business->id)
            ->active()
            ->ordered()
            ->get();

        return view('admin.products.create', [
            'categories' => $categories,
            'business' => $business,
        ]);
    }

    /**
     * Store a newly created product
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $business = Business::active()->first();
        $validated = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $this->imageService->uploadProductImage($request->file('image'));
        }

        $validated['business_id'] = $business->id;
        $validated['is_available'] = $request->boolean('is_available', true);
        $validated['is_featured'] = $request->boolean('is_featured', false);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Show the form for editing a product
     */
    public function edit(Product $product): View
    {
        $business = Business::active()->first();
        $categories = Category::where('business_id', $business->id)
            ->active()
            ->ordered()
            ->get();

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => $categories,
            'business' => $business,
        ]);
    }

    /**
     * Update the specified product
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $this->imageService->replace(
                $product->image,
                $request->file('image'),
                'products'
            );
        }

        $validated['is_available'] = $request->boolean('is_available', true);
        $validated['is_featured'] = $request->boolean('is_featured', false);

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product): RedirectResponse
    {
        // Delete image if exists
        $this->imageService->delete($product->image);

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
