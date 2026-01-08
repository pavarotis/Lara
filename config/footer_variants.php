<?php

declare(strict_types=1);

return [
    'simple' => [
        'name' => 'Simple',
        'show_opening_hours' => false,
        'show_social' => false,
        'show_newsletter' => false,
        'layout' => 'simple',
        'view' => 'themes.default.variants.footer-simple',
    ],
    'extended' => [
        'name' => 'Extended',
        'show_opening_hours' => true,
        'show_social' => true,
        'show_newsletter' => false,
        'layout' => 'extended',
        'view' => 'themes.default.variants.footer-extended',
    ],
    'business-info' => [
        'name' => 'Business Info',
        'show_opening_hours' => true,
        'show_social' => true,
        'show_newsletter' => true,
        'layout' => 'business-info',
        'view' => 'themes.default.variants.footer-business-info',
    ],
];
