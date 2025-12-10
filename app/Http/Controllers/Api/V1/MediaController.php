<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domain\Media\Models\Media;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\MediaResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MediaController extends BaseController
{
    // TODO: Add GetMediaService when Dev B completes Task B3

    public function index(Request $request, int $businessId): JsonResponse
    {
        $query = Media::where('business_id', $businessId)
            ->with(['folder', 'creator'])
            ->orderBy('created_at', 'desc');

        // Filters
        if ($request->filled('folder_id')) {
            $query->where('folder_id', $request->folder_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('path', 'like', '%'.$request->search.'%');
            });
        }

        $media = $query->paginate($request->get('per_page', 24));

        // Transform paginated items using MediaResource
        $media->setCollection(
            MediaResource::collection($media->items())->collection
        );

        return $this->paginated($media, 'Media retrieved successfully');
    }

    public function store(Request $request, int $businessId): JsonResponse
    {
        // TODO: Use UploadMediaService when Dev B completes Task B3
        // Validation will be handled by UploadMediaRequest
        // $media = $this->uploadMediaService->execute($request->file('file'), $request->folder_id);

        return $this->error('Media upload not yet implemented. Waiting for UploadMediaService.', [], 501);
    }

    public function show(int $businessId, int $id): JsonResponse
    {
        $media = Media::where('business_id', $businessId)
            ->with(['folder', 'creator'])
            ->find($id);

        if (! $media) {
            return $this->error('Media not found', [], 404);
        }

        return $this->success(new MediaResource($media), 'Media retrieved successfully');
    }

    public function destroy(int $businessId, int $id): JsonResponse
    {
        $media = Media::where('business_id', $businessId)->find($id);

        if (! $media) {
            return $this->error('Media not found', [], 404);
        }

        // TODO: Use DeleteMediaService when Dev B completes Task B3
        // $this->deleteMediaService->execute($media);

        return $this->error('Media deletion not yet implemented. Waiting for DeleteMediaService.', [], 501);
    }
}
