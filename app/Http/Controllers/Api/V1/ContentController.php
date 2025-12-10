<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domain\Businesses\Models\Business;
use App\Domain\Content\Models\Content;
use App\Domain\Content\Services\GetContentService;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\ContentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentController extends BaseController
{
    public function __construct(
        private GetContentService $getContentService
    ) {}

    /**
     * Get published content by slug for a business
     */
    public function show(int $businessId, string $slug): JsonResponse
    {
        $content = $this->getContentService->bySlug($businessId, $slug);

        if (! $content) {
            return $this->error('Content not found', [], 404);
        }

        return $this->success(new ContentResource($content), 'Content retrieved successfully');
    }

    /**
     * Get all published content for a business
     */
    public function index(Request $request, int $businessId): JsonResponse
    {
        $query = Content::where('business_id', $businessId)
            ->published()
            ->orderBy('published_at', 'desc');

        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                    ->orWhere('slug', 'like', '%'.$request->search.'%');
            });
        }

        $contents = $query->paginate($request->get('per_page', 15));

        // Transform paginated items using ContentResource
        $contents->setCollection(
            ContentResource::collection($contents->items())->collection
        );

        return $this->paginated($contents, 'Content retrieved successfully');
    }

    /**
     * Get content by type for a business
     */
    public function byType(int $businessId, string $type): JsonResponse
    {
        $contents = $this->getContentService->byType($businessId, $type);

        return $this->success(ContentResource::collection($contents), 'Content retrieved successfully');
    }
}
