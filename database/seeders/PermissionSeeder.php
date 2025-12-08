<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Auth\Models\Permission;
use App\Domain\Auth\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Content permissions
            ['name' => 'View Content', 'slug' => 'content.view', 'group' => 'content'],
            ['name' => 'Create Content', 'slug' => 'content.create', 'group' => 'content'],
            ['name' => 'Edit Content', 'slug' => 'content.edit', 'group' => 'content'],
            ['name' => 'Delete Content', 'slug' => 'content.delete', 'group' => 'content'],
            ['name' => 'Publish Content', 'slug' => 'content.publish', 'group' => 'content'],

            // Media permissions
            ['name' => 'View Media', 'slug' => 'media.view', 'group' => 'media'],
            ['name' => 'Upload Media', 'slug' => 'media.upload', 'group' => 'media'],
            ['name' => 'Delete Media', 'slug' => 'media.delete', 'group' => 'media'],

            // Catalog permissions
            ['name' => 'View Products', 'slug' => 'products.view', 'group' => 'catalog'],
            ['name' => 'Create Products', 'slug' => 'products.create', 'group' => 'catalog'],
            ['name' => 'Edit Products', 'slug' => 'products.edit', 'group' => 'catalog'],
            ['name' => 'Delete Products', 'slug' => 'products.delete', 'group' => 'catalog'],

            // Orders permissions
            ['name' => 'View Orders', 'slug' => 'orders.view', 'group' => 'orders'],
            ['name' => 'Manage Orders', 'slug' => 'orders.manage', 'group' => 'orders'],

            // User permissions
            ['name' => 'View Users', 'slug' => 'users.view', 'group' => 'users'],
            ['name' => 'Create Users', 'slug' => 'users.create', 'group' => 'users'],
            ['name' => 'Edit Users', 'slug' => 'users.edit', 'group' => 'users'],
            ['name' => 'Delete Users', 'slug' => 'users.delete', 'group' => 'users'],

            // Settings permissions
            ['name' => 'View Settings', 'slug' => 'settings.view', 'group' => 'settings'],
            ['name' => 'Manage Settings', 'slug' => 'settings.manage', 'group' => 'settings'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                array_merge($permission, ['is_system' => true])
            );
        }

        // Assign all permissions to admin role
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $allPermissions = Permission::all();
            $adminRole->permissions()->sync($allPermissions->pluck('id'));
        }

        // Assign content permissions to editor role
        $editorRole = Role::where('slug', 'editor')->first();
        if ($editorRole) {
            $contentPermissions = Permission::where('group', 'content')->get();
            $mediaPermissions = Permission::where('group', 'media')->get();
            $editorRole->permissions()->sync(
                $contentPermissions->merge($mediaPermissions)->pluck('id')
            );
        }
    }
}
