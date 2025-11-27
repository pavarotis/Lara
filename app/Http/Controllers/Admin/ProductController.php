<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Businesses\Models\Business;
use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
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
    public function store(Request $request): RedirectResponse
    {
        $business = Business::active()->first();

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string|max:255',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

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
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $product->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string|max:255',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

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
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}

