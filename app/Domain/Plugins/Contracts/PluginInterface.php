<?php

declare(strict_types=1);

namespace App\Domain\Plugins\Contracts;

interface PluginInterface
{
    public function getName(): string;

    public function getVersion(): string;

    public function getDescription(): string;

    /**
     * Register modules provided by the plugin.
     *
     * @return array<string, mixed>
     */
    public function registerModules(): array;

    /**
     * Boot plugin (routes, views, listeners, etc.).
     */
    public function boot(): void;

    /**
     * Optional plugin settings.
     *
     * @return array<string, mixed>
     */
    public function getSettings(): array;
}
