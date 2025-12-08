<?php

declare(strict_types=1);

namespace Tests\Feature\Catalog;

use App\Domain\Businesses\Models\Business;
use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewMenuTest extends TestCase
{
    use RefreshDatabase;

    private Business $business;

    protected function setUp(): void
    {
        parent::setUp();

        $this->business = Business::create([
            'name' => 'Test Cafe',
            'slug' => 'test-cafe',
            'type' => 'cafe',
            'settings' => ['delivery_enabled' => true],
            'is_active' => true,
        ]);
    }

    public function test_menu_page_loads_successfully(): void
    {
        $response = $this->get('/menu');

        $response->assertStatus(200);
        $response->assertViewIs('menu');
    }

    public function test_menu_displays_active_categories(): void
    {
        $activeCategory = Category::create([
            'business_id' => $this->business->id,
            'name' => 'Active Category',
            'slug' => 'active-category',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $inactiveCategory = Category::create([
            'business_id' => $this->business->id,
            'name' => 'Inactive Category',
            'slug' => 'inactive-category',
            'is_active' => false,
            'sort_order' => 2,
        ]);

        $response = $this->get('/menu');

        $response->assertSee('Active Category');
        $response->assertDontSee('Inactive Category');
    }

    public function test_category_page_shows_products(): void
    {
        $category = Category::create([
            'business_id' => $this->business->id,
            'name' => 'Coffee',
            'slug' => 'coffee',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'business_id' => $this->business->id,
            'category_id' => $category->id,
            'name' => 'Espresso',
            'slug' => 'espresso',
            'price' => 2.50,
            'is_available' => true,
            'sort_order' => 1,
        ]);

        $response = $this->get('/menu/coffee');

        $response->assertStatus(200);
        $response->assertSee('Espresso');
        $response->assertSee('2.50');
    }

    public function test_unavailable_products_are_hidden(): void
    {
        $category = Category::create([
            'business_id' => $this->business->id,
            'name' => 'Drinks',
            'slug' => 'drinks',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Product::create([
            'business_id' => $this->business->id,
            'category_id' => $category->id,
            'name' => 'Available Product',
            'slug' => 'available',
            'price' => 3.00,
            'is_available' => true,
            'sort_order' => 1,
        ]);

        Product::create([
            'business_id' => $this->business->id,
            'category_id' => $category->id,
            'name' => 'Unavailable Product',
            'slug' => 'unavailable',
            'price' => 3.00,
            'is_available' => false,
            'sort_order' => 2,
        ]);

        $response = $this->get('/menu/drinks');

        $response->assertSee('Available Product');
        $response->assertDontSee('Unavailable Product');
    }

    public function test_nonexistent_category_returns_404(): void
    {
        $response = $this->get('/menu/nonexistent-category');

        $response->assertStatus(404);
    }
}
