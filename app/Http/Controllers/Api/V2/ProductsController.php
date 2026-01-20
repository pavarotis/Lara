<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V2;

use App\Domain\Api\Services\ApiAuthService;
use App\Domain\Catalog\Models\Product;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V2\ProductResource;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function __construct(
        private ApiAuthService $apiAuthService
    ) {}

    /**
     * List all products
     */
    public function index(Request $request)
    {
        $business = $request->attributes->get('business');

        if (! $business) {
            return response()->json([
                'success' => false,
                'message' => 'Business not found',
                'errors' => [],
                'data' => null,
            ], 404);
        }

        // Check scope
        $apiKey = $request->attributes->get('api_key');
        if ($apiKey && ! $this->apiAuthService->hasScope($apiKey, 'read:products')) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient permissions',
                'errors' => [],
                'data' => null,
            ], 403);
        }

        $products = Product::where('business_id', $business->id)
            ->available()
            ->ordered()
            ->with('category')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Products retrieved successfully',
            'errors' => [],
            'data' => ProductResource::collection($products),
        ]);
    }

    /**
     * Get single product
     */
    public function show(Request $request, string $id)
    {
        $business = $request->attributes->get('business');

        if (! $business) {
            return response()->json([
                'success' => false,
                'message' => 'Business not found',
                'errors' => [],
                'data' => null,
            ], 404);
        }

        // Check scope
        $apiKey = $request->attributes->get('api_key');
        if ($apiKey && ! $this->apiAuthService->hasScope($apiKey, 'read:products')) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient permissions',
                'errors' => [],
                'data' => null,
            ], 403);
        }

        $product = Product::where('business_id', $business->id)
            ->where(function ($query) use ($id) {
                $query->where('id', $id)
                    ->orWhere('slug', $id);
            })
            ->with('category')
            ->first();

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'errors' => [],
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product retrieved successfully',
            'errors' => [],
            'data' => new ProductResource($product),
        ]);
    }
}
