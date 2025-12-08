<?php

declare(strict_types=1);

namespace App\Domain\Auth\Services;

use App\Models\User;

class CheckPermissionService
{
    /**
     * Check if user has permission
     */
    public function execute(User $user, string $permissionSlug): bool
    {
        return $user->hasPermission($permissionSlug);
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAny(User $user, array $permissionSlugs): bool
    {
        return $user->hasAnyPermission($permissionSlugs);
    }

    /**
     * Check if user has all of the given permissions
     */
    public function hasAll(User $user, array $permissionSlugs): bool
    {
        foreach ($permissionSlugs as $permissionSlug) {
            if (! $user->hasPermission($permissionSlug)) {
                return false;
            }
        }

        return true;
    }
}
