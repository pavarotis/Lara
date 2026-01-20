<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Plugin Providers
    |--------------------------------------------------------------------------
    |
    | Register plugin classes here. Each plugin must implement
    | App\Domain\Plugins\Contracts\PluginInterface.
    |
    */
    'providers' => [
        \Plugins\Example\ExamplePlugin::class,
    ],
];
