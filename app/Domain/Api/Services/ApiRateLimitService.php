<?php

declare(strict_types=1);

namespace App\Domain\Api\Services;

use App\Domain\Businesses\Models\Business;
use Illuminate\Support\Facades\Cache;

class ApiRateLimitService
{
    /**
     * Check if request is within rate limit
     */
    public function check(Business $business, string $endpoint): bool
    {
        $key = "api:rate_limit:{$business->id}:{$endpoint}";
        $limit = config('api.rate_limit', 100); // per minute

        $count = Cache::get($key, 0);

        if ($count >= $limit) {
            return false;
        }

        Cache::put($key, $count + 1, now()->addMinute());

        return true;
    }

    /**
     * Get remaining requests for a business/endpoint
     */
    public function getRemaining(Business $business, string $endpoint): int
    {
        $key = "api:rate_limit:{$business->id}:{$endpoint}";
        $limit = config('api.rate_limit', 100);
        $count = Cache::get($key, 0);

        return max(0, $limit - $count);
    }
}
