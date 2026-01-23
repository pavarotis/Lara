<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Businesses\Models\Business;
use App\Domain\Content\Models\Content;
use App\Domain\Content\Models\ContentType;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    public function run(): void
    {
        $business = Business::create([
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

        // Create home page content
        $this->createHomePage($business);
    }

    private function createHomePage(Business $business): void
    {
        $pageType = ContentType::where('slug', 'page')->first();

        if (! $pageType) {
            return; // ContentTypeSeeder should run first
        }

        Content::firstOrCreate(
            [
                'business_id' => $business->id,
                'slug' => '/',
            ],
            [
                'type' => 'page',
                'title' => 'Home',
                'body_json' => [
                    [
                        'type' => 'text',
                        'content' => '<h1>Welcome to '.$business->name.'</h1><p>This is your home page. You can edit it from the admin panel.</p>',
                    ],
                ],
                'status' => 'published',
                'published_at' => now(),
            ]
        );
    }
}
