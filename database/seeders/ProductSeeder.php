<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Businesses\Models\Business;
use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $business = Business::where('slug', 'demo-cafe')->first();

        if (!$business) {
            return;
        }

        $products = [
            // Καφέδες
            ['category' => 'coffees', 'name' => 'Espresso', 'slug' => 'espresso', 'price' => 2.00, 'sort_order' => 1],
            ['category' => 'coffees', 'name' => 'Cappuccino', 'slug' => 'cappuccino', 'price' => 3.50, 'sort_order' => 2],
            ['category' => 'coffees', 'name' => 'Freddo Espresso', 'slug' => 'freddo-espresso', 'price' => 3.00, 'sort_order' => 3],
            ['category' => 'coffees', 'name' => 'Freddo Cappuccino', 'slug' => 'freddo-cappuccino', 'price' => 3.50, 'sort_order' => 4],
            ['category' => 'coffees', 'name' => 'Ελληνικός', 'slug' => 'greek-coffee', 'price' => 2.50, 'sort_order' => 5],

            // Ροφήματα
            ['category' => 'beverages', 'name' => 'Σοκολάτα Ζεστή', 'slug' => 'hot-chocolate', 'price' => 3.50, 'sort_order' => 1],
            ['category' => 'beverages', 'name' => 'Τσάι', 'slug' => 'tea', 'price' => 2.50, 'sort_order' => 2],
            ['category' => 'beverages', 'name' => 'Φυσικός Χυμός Πορτοκάλι', 'slug' => 'orange-juice', 'price' => 4.00, 'sort_order' => 3],

            // Σνακ
            ['category' => 'snacks', 'name' => 'Κρουασάν Βουτύρου', 'slug' => 'butter-croissant', 'price' => 2.00, 'sort_order' => 1],
            ['category' => 'snacks', 'name' => 'Τοστ', 'slug' => 'toast', 'price' => 3.50, 'sort_order' => 2],
            ['category' => 'snacks', 'name' => 'Club Sandwich', 'slug' => 'club-sandwich', 'price' => 5.50, 'sort_order' => 3],

            // Γλυκά
            ['category' => 'desserts', 'name' => 'Cheesecake', 'slug' => 'cheesecake', 'price' => 5.00, 'sort_order' => 1],
            ['category' => 'desserts', 'name' => 'Τιραμισού', 'slug' => 'tiramisu', 'price' => 5.50, 'sort_order' => 2],
            ['category' => 'desserts', 'name' => 'Σοκολατόπιτα', 'slug' => 'chocolate-cake', 'price' => 4.50, 'sort_order' => 3],
        ];

        foreach ($products as $productData) {
            $category = Category::where('business_id', $business->id)
                ->where('slug', $productData['category'])
                ->first();

            if ($category) {
                Product::create([
                    'business_id' => $business->id,
                    'category_id' => $category->id,
                    'name' => $productData['name'],
                    'slug' => $productData['slug'],
                    'price' => $productData['price'],
                    'sort_order' => $productData['sort_order'],
                    'is_available' => true,
                    'is_featured' => false,
                ]);
            }
        }

        // Mark some products as featured
        Product::where('business_id', $business->id)
            ->whereIn('slug', ['cappuccino', 'freddo-cappuccino', 'cheesecake'])
            ->update(['is_featured' => true]);
    }
}

