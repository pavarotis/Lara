<?php

declare(strict_types=1);

namespace App\Domain\Content\Services;

use App\Domain\Content\Models\Content;

class DeleteContentService
{
    public function execute(Content $content): bool
    {
        return $content->delete();
    }
}
