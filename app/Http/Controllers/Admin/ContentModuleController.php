<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Content\Models\Content;
use App\Domain\Modules\Models\ContentModuleAssignment;
use App\Domain\Modules\Models\ModuleInstance;
use App\Domain\Modules\Services\AssignModuleToContentService;
use App\Domain\Modules\Services\GetModulesForRegionService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ContentModuleController extends Controller
{
    public function __construct(
        private GetModulesForRegionService $getModulesService,
        private AssignModuleToContentService $assignModuleService
    ) {}

    /**
     * Display modules management page for content
     */
    public function index(Content $content): View|RedirectResponse
    {
        $this->authorize('update', $content);

        $layout = $content->layout;

        if (! $layout) {
            return redirect()
                ->route('admin.content.edit', $content)
                ->with('error', 'Content must have a layout assigned before managing modules.');
        }

        $regions = $layout->getRegions();

        // Load modules per region (including disabled ones for admin view)
        $modulesByRegion = [];
        $allAssignments = ContentModuleAssignment::where('content_id', $content->id)
            ->with('moduleInstance')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('region');

        foreach ($regions as $region) {
            $modulesByRegion[$region] = $allAssignments->get($region, collect());
        }

        // Get available modules for adding (enabled modules for this business)
        $availableModules = ModuleInstance::forBusiness($content->business_id)
            ->enabled()
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->groupBy('type');

        // Get module types from config
        $moduleTypes = config('modules', []);

        return view('admin.content.modules', [
            'content' => $content,
            'layout' => $layout,
            'regions' => $regions,
            'modulesByRegion' => $modulesByRegion,
            'availableModules' => $availableModules,
            'moduleTypes' => $moduleTypes,
        ]);
    }

    /**
     * Add module to content region
     */
    public function addModule(Request $request, Content $content): RedirectResponse
    {
        $this->authorize('update', $content);

        $validated = $request->validate([
            'module_instance_id' => ['required', 'integer', 'exists:module_instances,id'],
            'region' => ['required', 'string'],
        ]);

        // Load module with business scoping
        $module = ModuleInstance::forBusiness($content->business_id)
            ->findOrFail($validated['module_instance_id']);

        // Get max sort_order for this region
        $maxSortOrder = ContentModuleAssignment::where('content_id', $content->id)
            ->where('region', $validated['region'])
            ->max('sort_order') ?? -1;

        try {
            $this->assignModuleService->assign(
                $content,
                $module,
                $validated['region'],
                $maxSortOrder + 1
            );

            return redirect()
                ->route('admin.content.modules', $content)
                ->with('success', 'Module added to region successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.content.modules', $content)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Reorder modules in region
     */
    public function reorder(Request $request, Content $content): \Illuminate\Http\JsonResponse
    {
        $this->authorize('update', $content);

        $validated = $request->validate([
            'region' => ['required', 'string'],
            'assignment_ids' => ['required', 'array'],
            'assignment_ids.*' => ['required', 'integer', 'exists:content_module_assignments,id'],
        ]);

        DB::transaction(function () use ($validated, $content) {
            foreach ($validated['assignment_ids'] as $index => $assignmentId) {
                ContentModuleAssignment::where('id', $assignmentId)
                    ->where('content_id', $content->id)
                    ->where('region', $validated['region'])
                    ->update(['sort_order' => $index]);
            }
        });

        return response()->json(['success' => true]);
    }

    /**
     * Toggle module enabled/disabled
     */
    public function toggle(ContentModuleAssignment $assignment): RedirectResponse
    {
        $this->authorize('update', $assignment->content);

        $module = $assignment->moduleInstance;
        $module->enabled = ! $module->enabled;
        $module->save();

        $status = $module->enabled ? 'enabled' : 'disabled';

        return redirect()
            ->route('admin.content.modules', $assignment->content)
            ->with('success', "Module {$status} successfully.");
    }

    /**
     * Remove module from region
     */
    public function remove(ContentModuleAssignment $assignment): RedirectResponse
    {
        $this->authorize('update', $assignment->content);

        $content = $assignment->content;
        $assignment->delete();

        return redirect()
            ->route('admin.content.modules', $content)
            ->with('success', 'Module removed from region successfully.');
    }
}
