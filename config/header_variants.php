<?php

declare(strict_types=1);

return [
    'minimal' => [
        'name' => 'Minimal',
        'sticky' => false,
        'show_phone' => false,
        'show_hours' => false,
        'show_social' => false,
        'layout' => 'minimal',
        'view' => 'themes.default.variants.header-minimal',
    ],
    'centered' => [
        'name' => 'Centered',
        'sticky' => true,
        'show_phone' => true,
        'show_hours' => false,
        'show_social' => true,
        'layout' => 'centered',
        'view' => 'themes.default.variants.header-centered',
    ],
    'with-topbar' => [
        'name' => 'With Top Bar',
        'sticky' => true,
        'show_phone' => true,
        'show_hours' => true,
        'show_social' => true,
        'layout' => 'with-topbar',
        'view' => 'themes.default.variants.header-with-topbar',
    ],
];
