<?php

/**
 * Content Configuration
 *
 * Defines content-related constants and default values.
 * Use ContentStatusHelper to access these values.
 */

return [
    /**
     * Content Status Values
     * These are the valid statuses for content items.
     */
    'statuses' => [
        'draft' => 'draft',
        'published' => 'published',
        'archived' => 'archived',
    ],

    /**
     * Default status for new content
     */
    'default_status' => 'draft',

    /**
     * Content Types
     * Valid content types in the system
     */
    'types' => [
        'page' => 'page',
        'article' => 'article',
        'post' => 'post',
    ],

    /**
     * Default content type
     */
    'default_type' => 'page',
];
