<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Route Helper
 *
 * Provides helper methods for route validation and exclusion patterns.
 * Use this class to ensure routes don't conflict with system routes.
 */
class RouteHelper
{
    /**
     * Get all excluded system routes
     */
    public static function getExcludedRoutes(): array
    {
        return array_merge(
            config('routes.auth', []),
            config('routes.admin', []),
            config('routes.api', []),
            config('routes.public', []),
            config('routes.seo', [])
        );
    }

    /**
     * Check if a route is excluded (system route)
     */
    public static function isExcluded(string $route): bool
    {
        return in_array($route, self::getExcludedRoutes(), true);
    }

    /**
     * Get regex pattern for excluding system routes from catch-all routes
     *
     * Usage:
     * Route::get('/{slug}', ...)->where('slug', RouteHelper::exclusionPattern());
     */
    public static function exclusionPattern(): string
    {
        $routes = self::getExcludedRoutes();
        if (empty($routes)) {
            return '.*';
        }

        $escaped = array_map(function ($route) {
            return preg_quote($route, '/');
        }, $routes);

        return '^(?!'.implode('|', $escaped).').*';
    }

    /**
     * Get regex pattern for business slug validation
     * Ensures business slugs don't conflict with system routes
     *
     * Usage:
     * Route::prefix('{business:slug}')->where('business', RouteHelper::businessSlugPattern())
     */
    public static function businessSlugPattern(): string
    {
        $routes = self::getExcludedRoutes();
        if (empty($routes)) {
            return '[a-z0-9-]+';
        }

        $escaped = array_map(function ($route) {
            return preg_quote($route, '/');
        }, $routes);

        return '^(?!'.implode('|', $escaped).')[a-z0-9-]+$';
    }

    /**
     * Validate that a route doesn't conflict with system routes
     * Throws exception if conflict is detected
     *
     * @throws \InvalidArgumentException
     */
    public static function validateRoute(string $route): void
    {
        if (self::isExcluded($route)) {
            throw new \InvalidArgumentException(
                "Route '{$route}' conflicts with a system route. ".
                'Please choose a different route name. '.
                'Excluded routes: '.implode(', ', self::getExcludedRoutes())
            );
        }
    }
}
