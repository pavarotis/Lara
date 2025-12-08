<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Businesses\Models\Business;
use App\Domain\Catalog\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $business = Business::where('slug', 'demo-cafe')->first();

        if (! $business) {
            return;
        }

        $categories = [
            ['name' => 'Καφέδες', 'slug' => 'coffees', 'sort_order' => 1],
            ['name' => 'Ροφήματα', 'slug' => 'beverages', 'sort_order' => 2],
            ['name' => 'Σνακ', 'slug' => 'snacks', 'sort_order' => 3],
            ['name' => 'Γλυκά', 'slug' => 'desserts', 'sort_order' => 4],
        ];

        foreach ($categories as $category) {
            Category::create([
                'business_id' => $business->id,
                'name' => $category['name'],
                'slug' => $category['slug'],
                'sort_order' => $category['sort_order'],
                'is_active' => true,
            ]);
        }
    }
}
