<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V2;

use App\Domain\Api\Services\ApiAuthService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V2\BusinessResource;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    public function __construct(
        private ApiAuthService $apiAuthService
    ) {}

    /**
     * Get business information
     */
    public function show(Request $request)
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
        if ($apiKey && ! $this->apiAuthService->hasScope($apiKey, 'read:business')) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient permissions',
                'errors' => [],
                'data' => null,
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Business retrieved successfully',
            'errors' => [],
            'data' => new BusinessResource($business),
        ]);
    }
}
