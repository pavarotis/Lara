<?php

declare(strict_types=1);

namespace App\Domain\Auth\Services;

use App\Domain\Auth\Models\Role;
use App\Models\User;

class RevokeRoleService
{
    /**
     * Revoke a role from a user
     */
    public function execute(User $user, Role|string $role): void
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)->firstOrFail();
        }

        // Don't allow removing admin role if user is the only admin
        if ($role->slug === 'admin' && $this->isOnlyAdmin($user)) {
            throw new \RuntimeException('Cannot remove the only admin role');
        }

        $user->roles()->detach($role->id);
    }

    /**
     * Check if user is the only admin
     */
    private function isOnlyAdmin(User $user): bool
    {
        $adminRole = Role::where('slug', 'admin')->first();

        if (! $adminRole) {
            return false;
        }

        $adminCount = $adminRole->users()->count();

        return $adminCount === 1 && $user->hasRole('admin');
    }
}
