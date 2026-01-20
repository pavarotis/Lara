<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class CacheMetricsService
{
    /**
     * Increment a cache metric counter.
     */
    public function increment(string $metric, ?int $businessId = null): void
    {
        $key = $this->metricKey($metric, $businessId);

        try {
            Cache::add($key, 0, now()->addDays(30));
            Cache::increment($key);
        } catch (\Throwable $e) {
            // Fail silently to avoid impacting the request flow.
        }
    }

    private function metricKey(string $metric, ?int $businessId = null): string
    {
        $metric = str_replace(' ', '_', $metric);

        if ($businessId === null) {
            return "cache_metrics:{$metric}";
        }

        return "cache_metrics:{$metric}:business:{$businessId}";
    }
}
