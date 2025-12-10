<?php

declare(strict_types=1);

namespace App\Domain\Content\Services;

use App\Domain\Content\Models\Content;
use App\Domain\Content\Models\ContentRevision;

class CreateRevisionService
{
    /**
     * Manually create a revision for content
     */
    public function execute(Content $content, ?int $userId = null): ContentRevision
    {
        return $content->revisions()->create([
            'body_json' => $content->body_json,
            'meta' => $content->meta,
            'user_id' => $userId ?? auth()->id(),
        ]);
    }
}
