<?php

declare(strict_types=1);

namespace App\Domain\Auth\Services;

use App\Domain\Auth\Models\Role;
use App\Models\User;

class AssignRoleService
{
    /**
     * Assign a role to a user
     */
    public function execute(User $user, Role|string $role): void
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)->firstOrFail();
        }

        if (! $user->roles()->where('id', $role->id)->exists()) {
            $user->roles()->attach($role->id);
        }
    }

    /**
     * Assign multiple roles to a user
     */
    public function assignMultiple(User $user, array $roleSlugs): void
    {
        $roles = Role::whereIn('slug', $roleSlugs)->get();

        foreach ($roles as $role) {
            if (! $user->roles()->where('id', $role->id)->exists()) {
                $user->roles()->attach($role->id);
            }
        }
    }
}
