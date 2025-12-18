<?php

declare(strict_types=1);

namespace App\Domain\Modules\Services;

use App\Domain\Modules\Models\ModuleInstance;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateModuleInstanceService
{
    public function __construct(
        private ValidateModuleTypeService $validateModuleTypeService
    ) {}

    /**
     * Update a module instance
     *
     * Note: Business isolation is enforced by the model's business_id field.
     * The module instance must be loaded with proper business scoping before calling this method.
     */
    public function execute(ModuleInstance $module, array $data): ModuleInstance
    {
        return DB::transaction(function () use ($module, $data) {
            // Prevent changing business_id (business isolation)
            if (isset($data['business_id']) && $data['business_id'] !== $module->business_id) {
                throw ValidationException::withMessages([
                    'business_id' => 'Cannot change business_id of a module instance.',
                ]);
            }

            // Validate width_mode if provided
            if (isset($data['width_mode'])) {
                $validWidthModes = ['contained', 'full', 'full-bg-contained-content'];
                if (! in_array($data['width_mode'], $validWidthModes, true)) {
                    throw ValidationException::withMessages([
                        'width_mode' => 'Invalid width mode. Must be one of: '.implode(', ', $validWidthModes),
                    ]);
                }
            }

            $module->update($data);

            return $module->fresh();
        });
    }
}
