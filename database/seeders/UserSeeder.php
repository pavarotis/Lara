<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Auth\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@larashop.test'],
            [
                'name' => 'Administrator',
                'email' => 'admin@larashop.test',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Assign admin role
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole && ! $adminUser->roles()->where('slug', 'admin')->exists()) {
            $adminUser->roles()->attach($adminRole->id);
        }

        // Create test user (non-admin)
        $testUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }
}
