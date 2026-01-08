<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V2;

use App\Domain\Api\Services\ApiAuthService;
use App\Domain\Content\Models\Content;
use App\Domain\Content\Services\GetContentService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V2\PageResource;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function __construct(
        private GetContentService $getContentService,
        private ApiAuthService $apiAuthService
    ) {}

    /**
     * List all published pages
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
        if ($apiKey && ! $this->apiAuthService->hasScope($apiKey, 'read:pages')) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient permissions',
                'errors' => [],
                'data' => null,
            ], 403);
        }

        $pages = Content::where('business_id', $business->id)
            ->published()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Pages retrieved successfully',
            'errors' => [],
            'data' => PageResource::collection($pages),
        ]);
    }

    /**
     * Get single page by slug
     */
    public function show(Request $request, string $slug)
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
        if ($apiKey && ! $this->apiAuthService->hasScope($apiKey, 'read:pages')) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient permissions',
                'errors' => [],
                'data' => null,
            ], 403);
        }

        $page = $this->getContentService->bySlug($business->id, $slug);

        if (! $page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found',
                'errors' => [],
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Page retrieved successfully',
            'errors' => [],
            'data' => new PageResource($page),
        ]);
    }
}
