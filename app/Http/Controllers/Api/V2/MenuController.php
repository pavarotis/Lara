<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V2;

use App\Domain\Api\Services\ApiAuthService;
use App\Domain\Catalog\Services\GetMenuForBusinessService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V2\MenuResource;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function __construct(
        private GetMenuForBusinessService $getMenuForBusinessService,
        private ApiAuthService $apiAuthService
    ) {}

    /**
     * Get menu structure (categories with products)
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
        if ($apiKey && ! $this->apiAuthService->hasScope($apiKey, 'read:menu')) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient permissions',
                'errors' => [],
                'data' => null,
            ], 403);
        }

        $menu = $this->getMenuForBusinessService->execute($business);

        return response()->json([
            'success' => true,
            'message' => 'Menu retrieved successfully',
            'errors' => [],
            'data' => new MenuResource($menu),
        ]);
    }
}
