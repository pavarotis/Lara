<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Businesses\Models\Business;
use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Product;
use App\Domain\Content\Models\Content;
use App\Domain\Content\Models\ContentType;
use Illuminate\Database\Seeder;

class GasStationSeeder extends Seeder
{
    public function run(): void
    {
        // Create Gas Station Business
        $business = Business::create([
            'name' => 'QuickFuel Station',
            'slug' => 'quickfuel',
            'type' => 'gas_station',
            'settings' => [
                'delivery_enabled' => false,
                'pickup_enabled' => true,
                'show_catalog_images' => true,
                'color_theme' => 'industrial',
                'currency' => 'EUR',
                'currency_symbol' => '€',
                'tax_rate' => 24,
                'minimum_order' => 0,
            ],
            'is_active' => true,
        ]);

        // Create Categories
        $fuel = Category::create([
            'business_id' => $business->id,
            'name' => 'Καύσιμα',
            'slug' => 'fuel',
            'description' => 'Βενζίνη, Diesel, LPG',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $snacks = Category::create([
            'business_id' => $business->id,
            'name' => 'Σνακ & Ποτά',
            'slug' => 'snacks',
            'description' => 'Για το δρόμο',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $carCare = Category::create([
            'business_id' => $business->id,
            'name' => 'Φροντίδα Αυτοκινήτου',
            'slug' => 'car-care',
            'description' => 'Λάδια, αξεσουάρ',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        // Fuel Products
        Product::create([
            'business_id' => $business->id,
            'category_id' => $fuel->id,
            'name' => 'Unleaded 95',
            'slug' => 'unleaded-95',
            'description' => 'Αμόλυβδη βενζίνη 95 οκτανίων',
            'price' => 1.85,
            'is_available' => true,
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        Product::create([
            'business_id' => $business->id,
            'category_id' => $fuel->id,
            'name' => 'Unleaded 100',
            'slug' => 'unleaded-100',
            'description' => 'Premium αμόλυβδη 100 οκτανίων',
            'price' => 2.05,
            'is_available' => true,
            'is_featured' => false,
            'sort_order' => 2,
        ]);

        Product::create([
            'business_id' => $business->id,
            'category_id' => $fuel->id,
            'name' => 'Diesel',
            'slug' => 'diesel',
            'description' => 'Κίνηση ντίζελ',
            'price' => 1.75,
            'is_available' => true,
            'is_featured' => true,
            'sort_order' => 3,
        ]);

        Product::create([
            'business_id' => $business->id,
            'category_id' => $fuel->id,
            'name' => 'LPG Autogas',
            'slug' => 'lpg',
            'description' => 'Υγραέριο κίνησης',
            'price' => 0.95,
            'is_available' => true,
            'is_featured' => false,
            'sort_order' => 4,
        ]);

        // Snacks Products
        Product::create([
            'business_id' => $business->id,
            'category_id' => $snacks->id,
            'name' => 'Νερό 500ml',
            'slug' => 'water-500',
            'description' => 'Εμφιαλωμένο νερό',
            'price' => 0.50,
            'is_available' => true,
            'is_featured' => false,
            'sort_order' => 1,
        ]);

        Product::create([
            'business_id' => $business->id,
            'category_id' => $snacks->id,
            'name' => 'Καφές Freddo',
            'slug' => 'freddo-coffee',
            'description' => 'Κρύος καφές espresso',
            'price' => 2.50,
            'is_available' => true,
            'is_featured' => true,
            'sort_order' => 2,
        ]);

        Product::create([
            'business_id' => $business->id,
            'category_id' => $snacks->id,
            'name' => 'Σάντουιτς',
            'slug' => 'sandwich',
            'description' => 'Τρίγωνο σάντουιτς',
            'price' => 3.00,
            'is_available' => true,
            'is_featured' => false,
            'sort_order' => 3,
        ]);

        // Car Care Products
        Product::create([
            'business_id' => $business->id,
            'category_id' => $carCare->id,
            'name' => 'Λάδι Κινητήρα 5W-40',
            'slug' => 'engine-oil-5w40',
            'description' => 'Συνθετικό λάδι 1L',
            'price' => 12.90,
            'is_available' => true,
            'is_featured' => false,
            'sort_order' => 1,
        ]);

        Product::create([
            'business_id' => $business->id,
            'category_id' => $carCare->id,
            'name' => 'Υγρό Παρμπρίζ',
            'slug' => 'windshield-fluid',
            'description' => 'Αντιπαγωτικό υγρό 4L',
            'price' => 5.50,
            'is_available' => true,
            'is_featured' => false,
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
