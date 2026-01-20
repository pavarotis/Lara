<?php

declare(strict_types=1);

namespace App\Domain\Content\Services;

use App\Domain\Content\Models\Content;
use App\Support\CacheInvalidationService;

class DeleteContentService
{
    public function execute(Content $content): bool
    {
        $deleted = $content->delete();

        if ($deleted) {
            app(CacheInvalidationService::class)->forgetPageCache($content->business_id);
        }

        return $deleted;
    }
}
