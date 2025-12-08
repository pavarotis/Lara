<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CMS Enabled
    |--------------------------------------------------------------------------
    |
    | Feature flag to enable/disable CMS functionality. When disabled,
    | v1 routes and views will be used. When enabled, CMS routes take over.
    |
    */
    'enabled' => env('CMS_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | CMS API Version
    |--------------------------------------------------------------------------
    |
    | Current API version for CMS endpoints.
    |
    */
    'api_version' => env('CMS_API_VERSION', 'v1'),
];
