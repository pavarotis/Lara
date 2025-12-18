<?php

declare(strict_types=1);

namespace App\Domain\Modules\Services;

use App\Domain\Content\Models\Content;
use App\Domain\Modules\Models\ModuleInstance;
use Illuminate\Database\Eloquent\Collection;

class GetModulesForRegionService
{
    /**
     * Get modules for a specific content region
     *
     * Returns: Collection of ModuleInstance ordered by sort_order
     * Logic:
     * 1. Load assignments for content + region
     * 2. Eager load module instances
     * 3. Filter enabled modules
     * 4. Order by sort_order
     */
    public function forContentRegion(Content $content, string $region): Collection
    {
        return ModuleInstance::query()
            ->whereHas('assignments', function ($query) use ($content, $region) {
                $query->where('content_id', $content->id)
                    ->where('region', $region);
            })
            ->enabled()
            ->with(['assignments' => function ($query) use ($content, $region) {
                $query->where('content_id', $content->id)
                    ->where('region', $region);
            }, 'business']) // Eager load business for theme resolution
            ->get()
            ->sortBy(function ($module) use ($region) {
                $assignment = $module->assignments
                    ->firstWhere('region', $region);

                return $assignment?->sort_order ?? 0;
            })
            ->values();
    }
}
