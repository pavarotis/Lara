<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Variable Values
    |--------------------------------------------------------------------------
    |
    | These are fallback values used when a variable doesn't exist in the database.
    | The actual values should be stored in the `variables` table.
    |
    */

    'defaults' => [
        'site_name' => 'My Store',
        'items_per_page' => 12,
        'contact_email' => 'contact@example.com',
        'currency' => 'EUR',
        'primary_color' => '#3b82f6',
        'meta_description' => 'Best online store',
        'google_analytics_id' => '',
        'enable_maintenance' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Cache settings for variables.
    |
    */

    'cache' => [
        'enabled' => true,
        'ttl' => 3600, // 1 hour in seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Category Icons
    |--------------------------------------------------------------------------
    |
    | Icons for each category in the Dynamic Settings page.
    |
    */

    'category_icons' => [
        'general' => 'heroicon-o-cog-6-tooth',
        'appearance' => 'heroicon-o-paint-brush',
        'seo' => 'heroicon-o-magnifying-glass',
        'email' => 'heroicon-o-envelope',
        'social' => 'heroicon-o-share',
        'payment' => 'heroicon-o-credit-card',
        'shipping' => 'heroicon-o-truck',
    ],
];
