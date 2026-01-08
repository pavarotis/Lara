<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | API Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for API endpoints.
    |
    */

    'rate_limit' => env('API_RATE_LIMIT', 100), // requests per minute

    /*
    |--------------------------------------------------------------------------
    | API Versioning
    |--------------------------------------------------------------------------
    |
    | Current API version.
    |
    */

    'version' => 'v2',

    /*
    |--------------------------------------------------------------------------
    | API Scopes
    |--------------------------------------------------------------------------
    |
    | Available API scopes for access control.
    |
    */

    'scopes' => [
        'read:business' => 'Read business information',
        'read:menu' => 'Read menu structure',
        'read:products' => 'Read products',
        'read:categories' => 'Read categories',
        'read:pages' => 'Read content pages',
        'read:orders' => 'Read orders',
        'write:orders' => 'Create orders',
        '*' => 'Full access',
    ],
];
