<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Businesses\Models\Business;
use App\Domain\Content\Services\CreateContentService;
use Illuminate\Console\Command;

class MigrateStaticPagesToCms extends Command
{
    protected $signature = 'cms:migrate-static-pages';

    protected $description = 'Migrate static pages (home, about, contact) to CMS content';

    public function __construct(
        private CreateContentService $createContentService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $business = Business::active()->firstOrFail();

        // Get admin user (first admin user or user ID 1 as fallback)
        $adminUser = \App\Models\User::where('is_admin', true)->first()
            ?? \App\Models\User::find(1);

        if (! $adminUser) {
            $this->error('No admin user found. Please create an admin user first.');

            return Command::FAILURE;
        }

        $this->info('Starting migration of static pages to CMS...');

        // Home page
        $this->migrateHomePage($business, $adminUser->id);

        // About page
        $this->migrateAboutPage($business, $adminUser->id);

        // Contact page
        $this->migrateContactPage($business, $adminUser->id);

        $this->info('Migration completed successfully!');
        $this->warn('Remember to update routes to use ContentController instead of static closures.');

        return Command::SUCCESS;
    }

    private function migrateHomePage(Business $business, int $userId): void
    {
        $this->info('Migrating home page...');

        $blocks = [
            [
                'type' => 'hero',
                'props' => [
                    'title' => 'Fresh & Delicious',
                    'subtitle' => 'Every Day',
                    'cta_text' => 'View Menu',
                    'cta_link' => '/menu',
                ],
            ],
            [
                'type' => 'text',
                'props' => [
                    'content' => '<h2>Why Choose Us</h2><p>We\'re committed to providing the best experience for our customers.</p>',
                    'alignment' => 'center',
                ],
            ],
            [
                'type' => 'text',
                'props' => [
                    'content' => '<h3>Fast Service</h3><p>Quick preparation and delivery to save your valuable time.</p>',
                    'alignment' => 'left',
                ],
            ],
            [
                'type' => 'text',
                'props' => [
                    'content' => '<h3>Quality Products</h3><p>Only the finest ingredients make it to your order.</p>',
                    'alignment' => 'left',
                ],
            ],
            [
                'type' => 'text',
                'props' => [
                    'content' => '<h3>Easy Ordering</h3><p>Simple online ordering with secure payment options.</p>',
                    'alignment' => 'left',
                ],
            ],
            [
                'type' => 'text',
                'props' => [
                    'content' => '<h2>Ready to Order?</h2><p>Browse our menu and place your order in just a few clicks.</p>',
                    'alignment' => 'center',
                ],
            ],
        ];

        $this->createContentService->execute([
            'business_id' => $business->id,
            'type' => 'page',
            'slug' => '/',
            'title' => 'Home',
            'body_json' => $blocks,
            'meta' => [
                'description' => 'Welcome to '.config('app.name', 'LaraShop').' - Fresh & Delicious Every Day',
                'keywords' => 'restaurant, food, delivery, online ordering',
            ],
            'status' => 'published',
            'published_at' => now(),
            'created_by' => $userId,
        ]);

        $this->info('✓ Home page migrated');
    }

    private function migrateAboutPage(Business $business, int $userId): void
    {
        $this->info('Migrating about page...');

        $blocks = [
            [
                'type' => 'text',
                'props' => [
                    'content' => '<h1>About Us</h1><p>Learn more about our story and mission.</p>',
                    'alignment' => 'center',
                ],
            ],
            [
                'type' => 'text',
                'props' => [
                    'content' => '<p>Welcome to <strong>'.config('app.name', 'LaraShop').'</strong>! We are passionate about delivering quality products and exceptional service to our customers.</p>',
                    'alignment' => 'left',
                ],
            ],
            [
                'type' => 'text',
                'props' => [
                    'content' => '<h2>Our Story</h2><p>Founded with a vision to make online ordering simple and enjoyable, we\'ve grown to become a trusted name in our community. Our commitment to quality and customer satisfaction drives everything we do.</p>',
                    'alignment' => 'left',
                ],
            ],
            [
                'type' => 'text',
                'props' => [
                    'content' => '<h2>Our Mission</h2><p>To provide fresh, high-quality products with convenient ordering options, whether you prefer pickup or delivery. We believe great food should be accessible to everyone.</p>',
                    'alignment' => 'left',
                ],
            ],
            [
                'type' => 'text',
                'props' => [
                    'content' => '<h2>Why Choose Us?</h2><ul><li>Fresh ingredients, prepared with care</li><li>Fast and reliable service</li><li>Easy online ordering</li><li>Friendly customer support</li></ul>',
                    'alignment' => 'left',
                ],
            ],
        ];

        $this->createContentService->execute([
            'business_id' => $business->id,
            'type' => 'page',
            'slug' => 'about',
            'title' => 'About Us',
            'body_json' => $blocks,
            'meta' => [
                'description' => 'Learn more about '.config('app.name', 'LaraShop').' - Our story, mission, and commitment to quality.',
                'keywords' => 'about, story, mission, quality',
            ],
            'status' => 'published',
            'published_at' => now(),
            'created_by' => $userId,
        ]);

        $this->info('✓ About page migrated');
    }

    private function migrateContactPage(Business $business, int $userId): void
    {
        $this->info('Migrating contact page...');

        $blocks = [
            [
                'type' => 'text',
                'props' => [
                    'content' => '<h1>Contact Us</h1><p>We\'d love to hear from you!</p>',
                    'alignment' => 'center',
                ],
            ],
            [
                'type' => 'text',
                'props' => [
                    'content' => '<h2>Get in Touch</h2><p><strong>Address:</strong><br>123 Main Street<br>Athens, Greece 10000</p><p><strong>Phone:</strong><br>+30 210 1234567</p><p><strong>Email:</strong><br>info@larashop.test</p><p><strong>Hours:</strong><br>Monday - Friday: 8:00 - 22:00<br>Saturday - Sunday: 9:00 - 23:00</p>',
                    'alignment' => 'left',
                ],
            ],
            [
                'type' => 'text',
                'props' => [
                    'content' => '<h2>Send us a Message</h2><p>Use the contact form below to send us a message. We\'ll get back to you as soon as possible.</p>',
                    'alignment' => 'left',
                ],
            ],
        ];

        $this->createContentService->execute([
            'business_id' => $business->id,
            'type' => 'page',
            'slug' => 'contact',
            'title' => 'Contact Us',
            'body_json' => $blocks,
            'meta' => [
                'description' => 'Contact '.config('app.name', 'LaraShop').' - Get in touch with us for questions, feedback, or support.',
                'keywords' => 'contact, support, help, get in touch',
            ],
            'status' => 'published',
            'published_at' => now(),
            'created_by' => $userId,
        ]);

        $this->info('✓ Contact page migrated');
        $this->warn('Note: Contact form functionality should be kept separate from CMS content.');
    }
}
