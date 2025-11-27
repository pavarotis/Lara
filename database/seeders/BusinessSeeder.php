<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Businesses\Models\Business;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    public function run(): void
    {
        Business::create([
            'name' => 'Demo Cafe',
            'slug' => 'demo-cafe',
            'type' => 'cafe',
            'settings' => [
                'delivery_enabled' => true,
                'show_catalog_images' => true,
                'color_theme' => 'warm',
                'currency' => 'EUR',
            ],
            'is_active' => true,
        ]);
    }
}

