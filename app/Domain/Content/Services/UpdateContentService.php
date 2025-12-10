<?php

declare(strict_types=1);

namespace App\Domain\Content\Services;

use App\Domain\Content\Models\Content;
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
                'user_id' => auth()->id(),
            ]);

            // Update content
            $content->update($data);

            return $content->fresh();
        });
    }
}
