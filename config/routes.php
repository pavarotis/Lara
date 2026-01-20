<?php

/**
 * Route Configuration
 *
 * This file defines system routes that must be protected from being matched
 * by catch-all routes (e.g., /{business:slug}, /{slug}).
 *
 * IMPORTANT: When adding new system routes, add them to the appropriate array below.
 */

return [
    /**
     * Authentication & User Routes
     * These routes are loaded from routes/auth.php and must NEVER be matched by catch-all routes.
     */
    'auth' => [
        'login',
        'register',
        'logout',
        'password',
        'email-verification',
        'verify-email',
        'forgot-password',
        'reset-password',
        'confirm-password',
    ],

    /**
     * Admin Routes
     * Admin panel routes (Filament handles most, but we list custom ones here).
     */
    'admin' => [
        'admin',
    ],

    /**
     * API Routes
     * API endpoints (loaded from routes/api.php).
     */
    'api' => [
        'api',
    ],

    /**
     * Public System Routes
     * Routes that are part of the core application functionality.
     */
    'public' => [
        'cart',
        'checkout',
        'menu',
        'dashboard',
        'profile',
        'preview',
    ],

    /**
     * SEO & System Files
     * Static files and SEO-related routes.
     */
    'seo' => [
        'sitemap.xml',
        'robots.txt',
    ],

    /**
     * Get all excluded routes as a single array
     *
     * Note: This is computed by RouteHelper::getExcludedRoutes()
     * We keep it here for reference, but RouteHelper handles the merging
     */
    'all' => null, // Computed by RouteHelper

    /**
     * Generate regex pattern for route exclusion
     * Use this in ->where() constraints to exclude system routes
     *
     * Note: This is computed dynamically to include all excluded routes
     */
    'exclusion_pattern' => null, // Computed by RouteHelper

    /**
     * Generate regex pattern for business slug validation
     * Ensures business slugs don't conflict with system routes
     *
     * Note: This is computed dynamically to include all excluded routes
     */
    'business_slug_pattern' => null, // Computed by RouteHelper
];
