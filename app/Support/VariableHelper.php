<?php

declare(strict_types=1);

namespace App\Support;

use App\Domain\Variables\Models\Variable;
use Illuminate\Support\Facades\Cache;

/**
 * VariableHelper
 *
 * Helper class for accessing variables in templates.
 * Usage: {{ App\Support\VariableHelper::get('key', $business) }}
 */
class VariableHelper
{
    /**
     * Get variable value for business
     */
    public static function get(string $key, ?\App\Domain\Businesses\Models\Business $business = null, mixed $default = null): mixed
    {
        if (! $business) {
            $business = \App\Domain\Businesses\Models\Business::active()->first();
        }

        if (! $business) {
            return $default;
        }

        $cacheKey = "variable:{$business->id}:{$key}";

        return Cache::remember($cacheKey, 3600, function () use ($business, $key, $default) {
            $variable = Variable::forBusiness($business->id)
                ->where('key', $key)
                ->first();

            if (! $variable) {
                return $default;
            }

            return $variable->getTypedValue();
        });
    }

    /**
     * Clear variable cache for business
     */
    public static function clearCache(?int $businessId = null): void
    {
        if ($businessId) {
            Cache::tags(["variables:{$businessId}"])->flush();
        } else {
            Cache::tags(['variables'])->flush();
        }
    }
}
