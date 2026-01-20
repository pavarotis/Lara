<?php

declare(strict_types=1);

namespace Plugins\Example;

use App\Domain\Plugins\Contracts\PluginInterface;

class ExamplePlugin implements PluginInterface
{
    public function getName(): string
    {
        return 'Example Plugin';
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }

    public function getDescription(): string
    {
        return 'Adds an example module';
    }

    public function registerModules(): array
    {
        return [
            'example_banner' => [
                'name' => 'Example Banner',
                'icon' => 'banner',
                'category' => 'marketing',
                'view' => 'example::modules.example-banner',
                'description' => 'Example plugin banner module',
            ],
        ];
    }

    public function boot(): void
    {
        // No-op for example plugin
    }

    public function getSettings(): array
    {
        return [];
    }
}
