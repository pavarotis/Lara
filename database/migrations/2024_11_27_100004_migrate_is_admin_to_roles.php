<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Create admin role if it doesn't exist
        $adminRoleId = DB::table('roles')->insertGetId([
            'name' => 'Administrator',
            'slug' => 'admin',
            'description' => 'Full system access',
            'is_system' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign admin role to all users with is_admin = true
        $adminUsers = DB::table('users')
            ->where('is_admin', true)
            ->get(['id']);

        foreach ($adminUsers as $user) {
            DB::table('role_user')->insert([
                'role_id' => $adminRoleId,
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Remove admin role assignments
        $adminRole = DB::table('roles')->where('slug', 'admin')->first();

        if ($adminRole) {
            DB::table('role_user')
                ->where('role_id', $adminRole->id)
                ->delete();

            DB::table('roles')->where('slug', 'admin')->delete();
        }
    }
};
