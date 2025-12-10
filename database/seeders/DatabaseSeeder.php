<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            // v2 RBAC & Settings (run first)
            RoleSeeder::class,
            PermissionSeeder::class,
            SettingsSeeder::class,
            // v2 Content Module
            ContentTypeSeeder::class,
            // Demo Cafe (default)
            BusinessSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            // Additional businesses
            GasStationSeeder::class,
            BakerySeeder::class,
        ]);
    }
}
