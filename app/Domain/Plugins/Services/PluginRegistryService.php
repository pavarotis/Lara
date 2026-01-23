<?php

declare(strict_types=1);

namespace App\Domain\Plugins\Services;

use App\Domain\Plugins\Contracts\PluginInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class PluginRegistryService
{
    /**
     * Discover plugin classes from config.
     *
     * @return array<int, class-string>
     */
    public function discover(): array
    {
        // Check if config service is available before using config helper
        if (! app()->bound('config')) {
            return [];
        }

        return config('plugins.providers', []);
    }

    /**
     * Register all discovered plugins.
     */
    public function registerAll(): void
    {
        foreach ($this->discover() as $pluginClass) {
            $this->register($pluginClass);
        }
    }

    /**
     * Register a single plugin by class name.
     */
    public function register(string $pluginClass): void
    {
        if (! class_exists($pluginClass)) {
            // Only log if config is available
            if (app()->bound('config') && app()->bound('log')) {
                Log::warning("Plugin class not found: {$pluginClass}");
            }

            return;
        }

        $plugin = app($pluginClass);

        if (! $plugin instanceof PluginInterface) {
            // Only log if config is available
            if (app()->bound('config') && app()->bound('log')) {
                Log::warning("Plugin class must implement PluginInterface: {$pluginClass}");
            }

            return;
        }

        $this->registerViews($plugin);
        $this->registerModules($plugin);

        $plugin->boot();
    }

    /**
     * Register plugin modules into config.
     */
    private function registerModules(PluginInterface $plugin): void
    {
        // Check if config service is available before using config helper
        if (! app()->bound('config')) {
            return;
        }

        $modules = $plugin->registerModules();

        foreach ($modules as $name => $moduleConfig) {
            $config = is_string($moduleConfig)
                ? ['class' => $moduleConfig]
                : Arr::wrap($moduleConfig);

            $config['name'] = $config['name'] ?? $name;
            $config['plugin'] = get_class($plugin);

            config(["modules.{$name}" => $config]);
        }
    }

    private function registerViews(PluginInterface $plugin): void
    {
        $pluginClass = get_class($plugin);
        $parts = explode('\\', $pluginClass);
        $folder = strtolower($parts[1] ?? class_basename($pluginClass));
        $basePath = base_path('plugins/'.$folder);
        $viewsPath = $basePath.'/resources/views';

        if (is_dir($viewsPath)) {
            $namespace = $folder;
            app('view')->addNamespace($namespace, $viewsPath);
        }
    }
}
