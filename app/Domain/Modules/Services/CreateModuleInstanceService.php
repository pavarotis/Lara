<?php

declare(strict_types=1);

namespace App\Domain\Modules\Services;

use App\Domain\Businesses\Models\Business;
use App\Domain\Modules\Models\ModuleInstance;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateModuleInstanceService
{
    public function __construct(
        private ValidateModuleTypeService $validateModuleTypeService
    ) {}

    /**
     * Create a module instance
     *
     * Validates:
     * - business_id exists
     * - type is in allowed modules list (config/modules.php)
     * - settings match module schema
     * - width_mode is valid enum
     */
    public function execute(array $data): ModuleInstance
    {
        return DB::transaction(function () use ($data) {
            // Validate business exists
            Business::findOrFail($data['business_id']);

            // Validate module type
            $this->validateModuleTypeService->validate($data['type']);

            // Set defaults
            $data['settings'] = $data['settings'] ?? [];
            $data['style'] = $data['style'] ?? null;
            $data['width_mode'] = $data['width_mode'] ?? 'contained';
            $data['enabled'] = $data['enabled'] ?? true;

            // Validate width_mode enum
            $validWidthModes = ['contained', 'full', 'full-bg-contained-content'];
            if (! in_array($data['width_mode'], $validWidthModes, true)) {
                throw ValidationException::withMessages([
                    'width_mode' => 'Invalid width mode. Must be one of: '.implode(', ', $validWidthModes),
                ]);
            }

            return ModuleInstance::create($data);
        });
    }
}
