<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V2;

use App\Domain\Api\Services\ApiAuthService;
use App\Domain\Catalog\Models\Category;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V2\CategoryResource;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function __construct(
        private ApiAuthService $apiAuthService
    ) {}

    /**
     * List all categories
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
        if ($apiKey && ! $this->apiAuthService->hasScope($apiKey, 'read:categories')) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient permissions',
                'errors' => [],
                'data' => null,
            ], 403);
        }

        $categories = Category::where('business_id', $business->id)
            ->active()
            ->ordered()
            ->with('products')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Categories retrieved successfully',
            'errors' => [],
            'data' => CategoryResource::collection($categories),
        ]);
    }

    /**
     * Get single category
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
        if ($apiKey && ! $this->apiAuthService->hasScope($apiKey, 'read:categories')) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient permissions',
                'errors' => [],
                'data' => null,
            ], 403);
        }

        $category = Category::where('business_id', $business->id)
            ->where(function ($query) use ($id) {
                $query->where('id', $id)
                    ->orWhere('slug', $id);
            })
            ->with('products')
            ->first();

        if (! $category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
                'errors' => [],
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Category retrieved successfully',
            'errors' => [],
            'data' => new CategoryResource($category),
        ]);
    }
}
