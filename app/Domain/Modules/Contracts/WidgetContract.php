<?php

declare(strict_types=1);

namespace App\Domain\Modules\Contracts;

interface WidgetContract
{
    /**
     * Render widget HTML
     *
     * @param  array  $config  Widget configuration
     * @param  array  $context  Rendering context (business, content, etc.)
     * @return string Rendered HTML
     */
    public function render(array $config, array $context = []): string;

    /**
     * Get required assets (CSS/JS)
     * Returns: ['css' => [...], 'js' => [...]]
     *
     * @return array<string, array<string>>
     */
    public function assets(): array;

    /**
     * Get critical CSS (inline)
     * Returns CSS that should be inlined in <head> for above-the-fold content
     *
     * @return string|null Critical CSS or null if not needed
     */
    public function criticalCss(): ?string;

    /**
     * Cache TTL in seconds
     * How long the widget output should be cached
     *
     * @return int Cache TTL in seconds
     */
    public function cacheTtl(): int;

    /**
     * Generate cache key for widget
     *
     * @param  array  $config  Widget configuration
     * @return string Cache key
     */
    public function cacheKey(array $config): string;
}
