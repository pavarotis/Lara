<?php

declare(strict_types=1);

namespace App\Domain\Content\Services;

use App\Domain\Content\Models\Content;
use App\Support\CacheInvalidationService;
use App\Support\ContentStatusHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PublishContentService
{
    public function __construct(
        private CreateRevisionService $createRevisionService
    ) {}

    /**
     * Publish content with revision backup and audit log
     */
    public function execute(Content $content, ?int $userId = null): Content
    {
        return DB::transaction(function () use ($content, $userId) {
            // 1. Create revision before publish (using existing service from Sprint 4.4)
            $this->createRevisionService->execute($content, $userId);

            // 2. Publish
            $content->update([
                'status' => ContentStatusHelper::published(),
                'published_at' => now(),
            ]);

            // 3. Log audit
            Log::info('Content published', [
                'content_id' => $content->id,
                'user_id' => $userId ?? auth()->id(),
                'slug' => $content->slug,
                'published_at' => $content->published_at,
            ]);

            // 4. Clear page cache for this business
            app(CacheInvalidationService::class)->forgetPageCache($content->business_id);

            return $content->fresh();
        });
    }
}
