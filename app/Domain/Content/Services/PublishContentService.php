<?php

declare(strict_types=1);

namespace App\Domain\Content\Services;

use App\Domain\Content\Models\Content;
use Illuminate\Support\Facades\DB;

class PublishContentService
{
    public function execute(Content $content): Content
    {
        return DB::transaction(function () use ($content) {
            $content->update([
                'status' => 'published',
                'published_at' => now(),
            ]);

            return $content->fresh();
        });
    }
}
