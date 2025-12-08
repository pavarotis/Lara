<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domain\Settings\Services\GetSettingsService;
use App\Domain\Settings\Services\UpdateSettingsService;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends BaseController
{
    public function __construct(
        private GetSettingsService $getSettingsService,
        private UpdateSettingsService $updateSettingsService
    ) {}

    /**
     * Get all settings
     */
    public function index(): JsonResponse
    {
        $settings = $this->getSettingsService->all();

        return $this->success($settings, 'Settings retrieved successfully');
    }

    /**
     * Get a specific setting
     */
    public function show(string $key): JsonResponse
    {
        $value = $this->getSettingsService->get($key);

        if ($value === null) {
            return $this->error('Setting not found', [], 404);
        }

        return $this->success(['key' => $key, 'value' => $value], 'Setting retrieved successfully');
    }

    /**
     * Create a new setting (admin only)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key' => 'required|string|unique:settings,key',
            'value' => 'nullable',
            'type' => 'required|in:string,boolean,json,integer,decimal',
            'description' => 'nullable|string',
            'group' => 'nullable|string',
        ]);

        $setting = $this->updateSettingsService->execute(
            $validated['key'],
            $validated['value'] ?? null,
            $validated['type'],
            $validated['group'] ?? 'general'
        );

        if (isset($validated['description'])) {
            $setting->description = $validated['description'];
        }

        if (isset($validated['group'])) {
            $setting->group = $validated['group'];
        }

        $setting->save();

        return $this->success($setting, 'Setting created successfully', 201);
    }

    /**
     * Update a setting (admin only)
     */
    public function update(Request $request, string $key): JsonResponse
    {
        $validated = $request->validate([
            'value' => 'required',
            'type' => 'sometimes|in:string,boolean,json,integer,decimal',
        ]);

        $setting = $this->updateSettingsService->execute(
            $key,
            $validated['value'],
            $validated['type'] ?? 'string'
        );

        return $this->success($setting, 'Setting updated successfully');
    }

    /**
     * Delete a setting (admin only)
     */
    public function destroy(string $key): JsonResponse
    {
        $setting = \App\Domain\Settings\Models\Setting::where('key', $key)->first();

        if (! $setting) {
            return $this->error('Setting not found', [], 404);
        }

        $setting->delete();

        return $this->success(null, 'Setting deleted successfully', 204);
    }
}
