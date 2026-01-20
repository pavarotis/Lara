<?php

declare(strict_types=1);

namespace App\Domain\Content\Services;

use App\Domain\Content\Models\Content;
use App\Support\ContentStatusHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateContentService
{
    public function execute(array $data): Content
    {
        return DB::transaction(function () use ($data) {
            // Auto-generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['title']);
            }

            // Set defaults
            $data['status'] = $data['status'] ?? ContentStatusHelper::default();
            $data['body_json'] = $data['body_json'] ?? [];
            $data['meta'] = $data['meta'] ?? [];
            $data['created_by'] = $data['created_by'] ?? auth()->id();

            // Create content
            $content = Content::create($data);

            // Create initial revision
            $content->revisions()->create([
                'body_json' => $content->body_json,
                'meta' => $content->meta,
                'user_id' => auth()->id(),
            ]);

            return $content;
        });
    }
}
