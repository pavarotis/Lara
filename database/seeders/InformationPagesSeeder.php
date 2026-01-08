<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Businesses\Models\Business;
use App\Domain\Content\Models\Content;
use App\Domain\Layouts\Models\Layout;
use App\Models\User;
use Illuminate\Database\Seeder;

class InformationPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $business = Business::active()->first();
        if (! $business) {
            $this->command->warn('No active business found. Skipping information pages seeder.');

            return;
        }

        $admin = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->orWhere('is_admin', true)->first();

        if (! $admin) {
            $this->command->warn('No admin user found. Skipping information pages seeder.');

            return;
        }

        $defaultLayout = Layout::where('business_id', $business->id)
            ->where('is_default', true)
            ->first();

        if (! $defaultLayout) {
            $defaultLayout = Layout::where('business_id', $business->id)->first();
        }

        $pages = [
            [
                'slug' => 'about',
                'title' => 'About Us',
                'type' => 'page',
            ],
            [
                'slug' => 'terms',
                'title' => 'Terms & Conditions',
                'type' => 'page',
            ],
            [
                'slug' => 'privacy',
                'title' => 'Privacy Policy',
                'type' => 'page',
            ],
            [
                'slug' => 'contact',
                'title' => 'Contact Us',
                'type' => 'page',
            ],
            [
                'slug' => 'delivery',
                'title' => 'Delivery Information',
                'type' => 'page',
            ],
        ];

        foreach ($pages as $pageData) {
            // Check if page already exists
            $existing = Content::where('business_id', $business->id)
                ->where('slug', $pageData['slug'])
                ->first();

            if ($existing) {
                $this->command->info("Page '{$pageData['slug']}' already exists. Skipping.");

                continue;
            }

            Content::create(array_merge($pageData, [
                'business_id' => $business->id,
                'layout_id' => $defaultLayout?->id,
                'created_by' => $admin->id,
                'status' => 'published',
                'published_at' => now(),
                'body_json' => [], // Will use layout modules
                'meta' => [],
            ]));

            $this->command->info("Created information page: {$pageData['slug']}");
        }
    }
}
