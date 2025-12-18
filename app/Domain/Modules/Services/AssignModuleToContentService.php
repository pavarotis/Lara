<?php

declare(strict_types=1);

namespace App\Domain\Modules\Services;

use App\Domain\Content\Models\Content;
use App\Domain\Layouts\Models\Layout;
use App\Domain\Modules\Models\ContentModuleAssignment;
use App\Domain\Modules\Models\ModuleInstance;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssignModuleToContentService
{
    /**
     * Assign module to content region
     *
     * Validates:
     * - Content has layout
     * - Layout has region
     * - Module belongs to same business
     * - No duplicate assignment
     */
    public function assign(Content $content, ModuleInstance $module, string $region, int $sortOrder = 0): ContentModuleAssignment
    {
        return DB::transaction(function () use ($content, $module, $region, $sortOrder) {
            // Validate content has layout
            if (! $content->layout_id) {
                throw ValidationException::withMessages([
                    'content' => 'Content must have a layout assigned before modules can be assigned.',
                ]);
            }

            // Validate layout has region (with business scoping)
            $layout = Layout::forBusiness($content->business_id)
                ->findOrFail($content->layout_id);
            if (! $layout->hasRegion($region)) {
                throw ValidationException::withMessages([
                    'region' => "Layout '{$layout->name}' does not have region '{$region}'.",
                ]);
            }

            // Validate module belongs to same business
            if ($module->business_id !== $content->business_id) {
                throw ValidationException::withMessages([
                    'module' => 'Module must belong to the same business as the content.',
                ]);
            }

            // Check for duplicate assignment
            $existing = ContentModuleAssignment::where('content_id', $content->id)
                ->where('module_instance_id', $module->id)
                ->where('region', $region)
                ->first();

            if ($existing) {
                throw ValidationException::withMessages([
                    'assignment' => 'Module is already assigned to this region for this content.',
                ]);
            }

            // Create assignment
            return ContentModuleAssignment::create([
                'content_id' => $content->id,
                'module_instance_id' => $module->id,
                'region' => $region,
                'sort_order' => $sortOrder,
            ]);
        });
    }
}
