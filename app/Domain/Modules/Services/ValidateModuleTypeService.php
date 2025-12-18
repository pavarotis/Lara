<?php

declare(strict_types=1);

namespace App\Domain\Modules\Services;

use Illuminate\Validation\ValidationException;

class ValidateModuleTypeService
{
    /**
     * Validate that module type exists in config/modules.php
     */
    public function validate(string $moduleType): void
    {
        $modules = config('modules', []);

        if (! isset($modules[$moduleType])) {
            throw ValidationException::withMessages([
                'type' => "Module type '{$moduleType}' is not registered. Available types: ".implode(', ', array_keys($modules)),
            ]);
        }
    }
}
