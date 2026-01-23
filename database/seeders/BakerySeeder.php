<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Businesses\Models\Business;
use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Product;
use App\Domain\Content\Models\Content;
use App\Domain\Content\Models\ContentType;
use Illuminate\Database\Seeder;

class BakerySeeder extends Seeder
{
    public function run(): void
    {
        // Create Bakery Business
        $business = Business::create([
            'name' => 'Artisan Bakery',
            'slug' => 'artisan-bakery',
            'type' => 'bakery',
            'settings' => [
                'delivery_enabled' => true,
                'pickup_enabled' => true,
                'show_catalog_images' => true,
                'color_theme' => 'warm',
                'currency' => 'EUR',
                'currency_symbol' => '€',
                'tax_rate' => 13, // Lower VAT for food
                'minimum_order' => 5.00,
            ],
            'is_active' => true,
        ]);

        // Create Categories
        $breads = Category::create([
            'business_id' => $business->id,
            'name' => 'Ψωμιά',
            'slug' => 'breads',
            'description' => 'Φρέσκα ψωμιά κάθε μέρα',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $pastries = Category::create([
            'business_id' => $business->id,
            'name' => 'Αρτοσκευάσματα',
            'slug' => 'pastries',
            'description' => 'Τυρόπιτες, κρουασάν, μπουγάτσες',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $sweets = Category::create([
            'business_id' => $business->id,
            'name' => 'Γλυκά',
            'slug' => 'sweets',
            'description' => 'Κέικ, μπισκότα, τούρτες',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        $coffee = Category::create([
            'business_id' => $business->id,
            'name' => 'Καφέδες',
            'slug' => 'coffee',
            'description' => 'Ζεστοί και κρύοι καφέδες',
            'sort_order' => 4,
            'is_active' => true,
        ]);

        // Bread Products
        Product::create([
            'business_id' => $business->id,
            'category_id' => $breads->id,
            'name' => 'Χωριάτικο Ψωμί',
            'slug' => 'village-bread',
            'description' => 'Παραδοσιακό ψωμί με προζύμι',
            'price' => 2.50,
            'is_available' => true,
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        Product::create([
            'business_id' => $business->id,
            'category_id' => $breads->id,
            'name' => 'Ψωμί Ολικής',
            'slug' => 'whole-wheat',
            'description' => 'Ψωμί ολικής άλεσης',
            'price' => 3.00,
            'is_available' => true,
            'is_featured' => false,
            'sort_order' => 2,
        ]);

        Product::create([
            'business_id' => $business->id,
            'category_id' => $breads->id,
            'name' => 'Μπαγκέτα',
            'slug' => 'baguette',
            'description' => 'Γαλλική μπαγκέτα',
            'price' => 1.80,
            'is_available' => true,
            'is_featured' => false,
            'sort_order' => 3,
        ]);

        // Pastry Products
        Product::create([
            'business_id' => $business->id,
            'category_id' => $pastries->id,
            'name' => 'Τυρόπιτα Κουρού',
            'slug' => 'cheese-pie',
            'description' => 'Τυρόπιτα με φέτα',
            'price' => 2.20,
            'is_available' => true,
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        Product::create([
            'business_id' => $business->id,
            'category_id' => $pastries->id,
            'name' => 'Σπανακόπιτα',
            'slug' => 'spinach-pie',
            'description' => 'Με φρέσκο σπανάκι',
            'price' => 2.50,
            'is_available' => true,
            'is_featured' => false,
            'sort_order' => 2,
        ]);

        Product::create([
            'business_id' => $business->id,
            'category_id' => $pastries->id,
            'name' => 'Κρουασάν Βουτύρου',
            'slug' => 'butter-croissant',
            'description' => 'Αφράτο κρουασάν',
            'price' => 1.80,
            'is_available' => true,
            'is_featured' => true,
            'sort_order' => 3,
        ]);

        Product::create([
            'business_id' => $business->id,
            'category_id' => $pastries->id,
            'name' => 'Μπουγάτσα Κρέμα',
            'slug' => 'bougatsa-cream',
            'description' => 'Παραδοσιακή μπουγάτσα',
            'price' => 3.50,
            'is_available' => true,
            'is_featured' => false,
            'sort_order' => 4,
        ]);

        // Sweet Products
        Product::create([
            'business_id' => $business->id,
            'category_id' => $sweets->id,
            'name' => 'Κέικ Σοκολάτας',
            'slug' => 'chocolate-cake',
            'description' => 'Σοκολατένιο κέικ (κομμάτι)',
            'price' => 4.00,
            'is_available' => true,
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        Product::create([
            'business_id' => $business->id,
            'category_id' => $sweets->id,
            'name' => 'Cookies',
            'slug' => 'cookies',
            'description' => 'Μπισκότα βουτύρου (3 τμχ)',
            'price' => 2.50,
            'is_available' => true,
            'is_featured' => false,
            'sort_order' => 2,
        ]);

        // Coffee Products
        Product::create([
            'business_id' => $business->id,
            'category_id' => $coffee->id,
            'name' => 'Espresso',
            'slug' => 'espresso',
            'description' => 'Διπλό espresso',
            'price' => 1.80,
            'is_available' => true,
            'is_featured' => false,
            'sort_order' => 1,
        ]);

        Product::create([
            'business_id' => $business->id,
            'category_id' => $coffee->id,
            'name' => 'Cappuccino',
            'slug' => 'cappuccino',
            'description' => 'Με αφρόγαλα',
            'price' => 3.00,
            'is_available' => true,
            'is_featured' => true,
            'sort_order' => 2,
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
