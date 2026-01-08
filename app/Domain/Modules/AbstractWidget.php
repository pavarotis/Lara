<?php

declare(strict_types=1);

namespace App\Domain\Modules;

use App\Domain\Modules\Contracts\WidgetContract;

abstract class AbstractWidget implements WidgetContract
{
    /**
     * Default assets (empty - override in child classes)
     *
     * @return array<string, array<string>>
     */
    public function assets(): array
    {
        return [
            'css' => [],
            'js' => [],
        ];
    }

    /**
     * Default critical CSS (null - override in child classes)
     */
    public function criticalCss(): ?string
    {
        return null;
    }

    /**
     * Default cache TTL (1 hour)
     */
    public function cacheTtl(): int
    {
        return 3600;
    }

    /**
     * Default cache key generation
     */
    public function cacheKey(array $config): string
    {
        return 'widget:'.static::class.':'.md5(json_encode($config));
    }
}
