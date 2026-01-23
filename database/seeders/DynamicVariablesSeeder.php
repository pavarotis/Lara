<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Businesses\Models\Business;
use App\Domain\Variables\Models\Variable;
use Illuminate\Database\Seeder;

class DynamicVariablesSeeder extends Seeder
{
    /**
     * Seed dynamic variables with categories for testing
     * This seeder demonstrates how to create variables that will automatically
     * appear in the DynamicSettings page organized by category
     */
    public function run(): void
    {
        $business = Business::active()->first();

        if (! $business) {
            $this->command->warn('No active business found. Skipping dynamic variables seeding.');

            return;
        }

        $this->command->info('Seeding dynamic variables with categories...');

        $variables = [
            // General Category
            [
                'key' => 'site_name',
                'value' => 'My Awesome Store',
                'type' => 'string',
                'category' => 'general',
                'description' => 'Website Name',
            ],
            [
                'key' => 'items_per_page',
                'value' => '12',
                'type' => 'number',
                'category' => 'general',
                'description' => 'Items per Page',
            ],
            [
                'key' => 'enable_maintenance',
                'value' => '0',
                'type' => 'boolean',
                'category' => 'general',
                'description' => 'Enable Maintenance Mode',
            ],

            // Appearance Category
            [
                'key' => 'primary_color',
                'value' => '#3b82f6',
                'type' => 'string',
                'category' => 'appearance',
                'description' => 'Primary Brand Color',
            ],
            [
                'key' => 'theme_colors',
                'value' => json_encode([
                    'primary' => '#3b82f6',
                    'secondary' => '#8b5cf6',
                    'accent' => '#10b981',
                ]),
                'type' => 'json',
                'category' => 'appearance',
                'description' => 'Theme Color Palette',
            ],
            [
                'key' => 'logo_width',
                'value' => '200',
                'type' => 'number',
                'category' => 'appearance',
                'description' => 'Logo Width (px)',
            ],

            // SEO Category
            [
                'key' => 'meta_description',
                'value' => 'Best online store for all your needs',
                'type' => 'string',
                'category' => 'seo',
                'description' => 'Default Meta Description',
            ],
            [
                'key' => 'google_analytics_id',
                'value' => '',
                'type' => 'string',
                'category' => 'seo',
                'description' => 'Google Analytics ID',
            ],
            [
                'key' => 'seo_keywords',
                'value' => json_encode(['online store', 'shopping', 'ecommerce']),
                'type' => 'json',
                'category' => 'seo',
                'description' => 'SEO Keywords',
            ],

            // Email Category
            [
                'key' => 'contact_email',
                'value' => 'contact@example.com',
                'type' => 'string',
                'category' => 'email',
                'description' => 'Contact Email Address',
            ],
            [
                'key' => 'email_from_name',
                'value' => 'My Store',
                'type' => 'string',
                'category' => 'email',
                'description' => 'Email From Name',
            ],
            [
                'key' => 'enable_email_notifications',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'email',
                'description' => 'Enable Email Notifications',
            ],

            // Social Category
            [
                'key' => 'facebook_url',
                'value' => 'https://facebook.com/mystore',
                'type' => 'string',
                'category' => 'social',
                'description' => 'Facebook Page URL',
            ],
            [
                'key' => 'twitter_url',
                'value' => 'https://twitter.com/mystore',
                'type' => 'string',
                'category' => 'social',
                'description' => 'Twitter Profile URL',
            ],
            [
                'key' => 'social_links',
                'value' => json_encode([
                    'facebook' => 'https://facebook.com/mystore',
                    'twitter' => 'https://twitter.com/mystore',
                    'instagram' => 'https://instagram.com/mystore',
                ]),
                'type' => 'json',
                'category' => 'social',
                'description' => 'All Social Media Links',
            ],

            // Payment Category
            [
                'key' => 'currency',
                'value' => 'EUR',
                'type' => 'string',
                'category' => 'payment',
                'description' => 'Default Currency',
            ],
            [
                'key' => 'payment_methods',
                'value' => json_encode(['credit_card', 'paypal', 'bank_transfer']),
                'type' => 'json',
                'category' => 'payment',
                'description' => 'Available Payment Methods',
            ],
            [
                'key' => 'enable_paypal',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'payment',
                'description' => 'Enable PayPal Payments',
            ],

            // Shipping Category
            [
                'key' => 'free_shipping_threshold',
                'value' => '50.00',
                'type' => 'number',
                'category' => 'shipping',
                'description' => 'Free Shipping Threshold (€)',
            ],
            [
                'key' => 'default_shipping_cost',
                'value' => '5.99',
                'type' => 'number',
                'category' => 'shipping',
                'description' => 'Default Shipping Cost (€)',
            ],
            [
                'key' => 'shipping_zones',
                'value' => json_encode([
                    'domestic' => ['cost' => 5.99, 'days' => 3],
                    'europe' => ['cost' => 12.99, 'days' => 7],
                    'worldwide' => ['cost' => 25.99, 'days' => 14],
                ]),
                'type' => 'json',
                'category' => 'shipping',
                'description' => 'Shipping Zones Configuration',
            ],
        ];

        foreach ($variables as $varData) {
            Variable::firstOrCreate(
                [
                    'business_id' => $business->id,
                    'key' => $varData['key'],
                ],
                [
                    'value' => $varData['value'],
                    'type' => $varData['type'],
                    'category' => $varData['category'],
                    'description' => $varData['description'],
                ]
            );
        }

        $this->command->info('Dynamic variables seeded successfully!');
        $this->command->info('Categories created: '.implode(', ', array_unique(array_column($variables, 'category'))));
    }
}
