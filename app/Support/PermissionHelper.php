<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Permission Helper
 *
 * Provides centralized permission checking logic.
 * Use this class to ensure consistent permission checks across the application.
 */
class PermissionHelper
{
    /**
     * Check if user is admin
     * Handles both hasRole('admin') and isAdmin() methods
     *
     * @param  \App\Models\User|null  $user  User to check (defaults to authenticated user)
     */
    public static function isAdmin(?User $user = null): bool
    {
        $user = $user ?? Auth::user();

        if (! $user) {
            return false;
        }

        // Try hasRole first (preferred method)
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole('admin');
        }

        // Fallback to isAdmin method
        if (method_exists($user, 'isAdmin')) {
            return $user->isAdmin();
        }

        // Fallback to is_admin property
        return $user->is_admin ?? false;
    }

    /**
     * Check if user can view unpublished content
     * Currently only admins can view unpublished content
     */
    public static function canViewUnpublished(?User $user = null): bool
    {
        return self::isAdmin($user);
    }

    /**
     * Check if user can edit content
     */
    public static function canEditContent(?User $user = null): bool
    {
        return self::isAdmin($user);
    }

    /**
     * Check if user can publish content
     */
    public static function canPublishContent(?User $user = null): bool
    {
        return self::isAdmin($user);
    }

    /**
     * Require admin access
     * Throws 403 if user is not admin
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    public static function requireAdmin(?User $user = null): void
    {
        if (! self::isAdmin($user)) {
            abort(403, 'Admin access required');
        }
    }

    /**
     * Require authenticated user
     * Throws 401 if user is not authenticated
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    public static function requireAuth(): void
    {
        if (! Auth::check()) {
            abort(401, 'Authentication required');
        }
    }

    /**
     * Check if user has a specific role
     */
    public static function hasRole(string $role, ?User $user = null): bool
    {
        $user = $user ?? Auth::user();

        if (! $user) {
            return false;
        }

        if (method_exists($user, 'hasRole')) {
            return $user->hasRole($role);
        }

        return false;
    }

    /**
     * Check if user has any of the specified roles
     */
    public static function hasAnyRole(array $roles, ?User $user = null): bool
    {
        foreach ($roles as $role) {
            if (self::hasRole($role, $user)) {
                return true;
            }
        }

        return false;
    }
}
