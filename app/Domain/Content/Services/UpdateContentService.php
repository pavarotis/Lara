<?php

declare(strict_types=1);

namespace App\Domain\Content\Services;

use App\Domain\Content\Models\Content;
use App\Support\CacheInvalidationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateContentService
{
    public function execute(Content $content, array $data): Content
    {
        return DB::transaction(function () use ($content, $data) {
            // Auto-create revision before update
            $content->revisions()->create([
                'body_json' => $content->body_json,
                'meta' => $content->meta,
                'user_id' => Auth::id(),
            ]);

            // Update content
            $content->update($data);

            // Invalidate cached pages for this business
            app(CacheInvalidationService::class)->forgetPageCache($content->business_id);

            return $content->fresh();
        });
    }
}
