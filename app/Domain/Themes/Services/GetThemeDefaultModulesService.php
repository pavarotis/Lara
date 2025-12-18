<?php

declare(strict_types=1);

namespace App\Domain\Themes\Services;

use App\Domain\Modules\Services\CreateModuleInstanceService;
use Illuminate\Support\Facades\Log;

/**
 * GetThemeDefaultModulesService
 *
 * Loads theme default modules from JSON file or database.
 * Used when page has no overrides.
 */
class GetThemeDefaultModulesService
{
    public function __construct(
        private CreateModuleInstanceService $createModuleInstanceService
    ) {}

    /**
     * Get default modules for theme
     *
     * @param  string  $theme  Theme name
     * @param  int  $businessId  Business ID
     * @return array Array of ModuleInstance models
     */
    public function getDefaultsForTheme(string $theme, int $businessId): array
    {
        // Option 1: Load from JSON file
        $jsonPath = resource_path("themes/{$theme}/default-modules.json");
        if (file_exists($jsonPath)) {
            try {
                $defaults = json_decode(file_get_contents($jsonPath), true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($defaults)) {
                    return $this->createModuleInstances($defaults, $businessId);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to load theme defaults from JSON: '.$e->getMessage());
            }
        }

        // Option 2: Load from database (theme_default_modules table)
        // (Future implementation - not in Sprint 4 scope)

        // Option 3: Return empty array (no defaults)
        return [];
    }

    /**
     * Create module instances from defaults
     *
     * @param  array  $defaults  Array of module default configurations
     * @param  int  $businessId  Business ID
     * @return array Array of ModuleInstance models
     */
    private function createModuleInstances(array $defaults, int $businessId): array
    {
        $instances = [];

        foreach ($defaults as $default) {
            try {
                // Validate default structure
                if (! isset($default['type']) || ! isset($default['region'])) {
                    Log::warning('Invalid default module structure: missing type or region');

                    continue;
                }

                // Check if module instance already exists (by name or settings)
                // For now, we'll create new instances each time
                // In future, we might want to cache or reuse instances

                $moduleData = [
                    'business_id' => $businessId,
                    'type' => $default['type'],
                    'name' => null, // Default modules are not reusable
                    'settings' => $default['settings'] ?? [],
                    'style' => $default['style'] ?? [],
                    'width_mode' => $default['width_mode'] ?? 'contained',
                    'enabled' => $default['enabled'] ?? true,
                ];

                $instance = $this->createModuleInstanceService->create($moduleData);
                $instances[] = $instance;
            } catch (\Exception $e) {
                Log::warning('Failed to create default module instance: '.$e->getMessage());

                continue;
            }
        }

        return $instances;
    }
}
