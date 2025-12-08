<?php

declare(strict_types=1);

namespace App\Domain\Auth\Services;

use App\Domain\Auth\Models\Role;
use App\Models\User;

class MigrateAdminToRolesService
{
    public function __construct(
        private AssignRoleService $assignRoleService
    ) {}

    /**
     * Migrate existing is_admin users to admin role
     */
    public function execute(): int
    {
        // Ensure admin role exists
        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Administrator',
                'description' => 'Full system access',
                'is_system' => true,
            ]
        );

        // Find all users with is_admin = true that don't have admin role
        $adminUsers = User::where('is_admin', true)
            ->whereDoesntHave('roles', function ($query) use ($adminRole) {
                $query->where('roles.id', $adminRole->id);
            })
            ->get();

        $migrated = 0;

        foreach ($adminUsers as $user) {
            $this->assignRoleService->execute($user, $adminRole);
            $migrated++;
        }

        return $migrated;
    }
}
