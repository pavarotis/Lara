<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Domain\Businesses\Models\Business;
use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $user;
    private Business $business;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->user = User::factory()->create(['is_admin' => false]);

        $this->business = Business::create([
            'name' => 'Test Cafe',
            'slug' => 'test-cafe',
            'type' => 'cafe',
            'is_active' => true,
        ]);

        $this->category = Category::create([
            'business_id' => $this->business->id,
            'name' => 'Coffee',
            'slug' => 'coffee',
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    public function test_guest_cannot_access_admin_products(): void
    {
        $response = $this->get('/admin/products');

        $response->assertRedirect('/login');
    }

    public function test_non_admin_cannot_access_admin_products(): void
    {
        $response = $this->actingAs($this->user)->get('/admin/products');

        $response->assertStatus(403);
    }

    public function test_admin_can_view_products_list(): void
    {
        Product::create([
            'business_id' => $this->business->id,
            'category_id' => $this->category->id,
            'name' => 'Espresso',
            'slug' => 'espresso',
            'price' => 2.50,
            'is_available' => true,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/products');

        $response->assertStatus(200);
        $response->assertSee('Espresso');
    }

    public function test_admin_can_view_create_product_form(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/products/create');

        $response->assertStatus(200);
        $response->assertSee('Coffee'); // Category should be in dropdown
    }

    public function test_admin_can_create_product(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/products', [
            'category_id' => $this->category->id,
            'name' => 'New Product',
            'slug' => 'new-product',
            'description' => 'A test product',
            'price' => 5.99,
            'is_available' => true,
            'is_featured' => false,
            'sort_order' => 1,
        ]);

        $response->assertRedirect('/admin/products');

        $this->assertDatabaseHas('products', [
            'name' => 'New Product',
            'slug' => 'new-product',
            'price' => 5.99,
        ]);
    }

    public function test_admin_can_view_edit_product_form(): void
    {
        $product = Product::create([
            'business_id' => $this->business->id,
            'category_id' => $this->category->id,
            'name' => 'Latte',
            'slug' => 'latte',
            'price' => 4.00,
            'is_available' => true,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/products/{$product->id}/edit");

        $response->assertStatus(200);
        $response->assertSee('Latte');
    }

    public function test_admin_can_update_product(): void
    {
        $product = Product::create([
            'business_id' => $this->business->id,
            'category_id' => $this->category->id,
            'name' => 'Old Name',
            'slug' => 'old-name',
            'price' => 3.00,
            'is_available' => true,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/products/{$product->id}", [
            'category_id' => $this->category->id,
            'name' => 'Updated Name',
            'slug' => 'updated-name',
            'price' => 4.50,
            'is_available' => true,
            'sort_order' => 1,
        ]);

        $response->assertRedirect('/admin/products');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Name',
            'price' => 4.50,
        ]);
    }

    public function test_admin_can_delete_product(): void
    {
        $product = Product::create([
            'business_id' => $this->business->id,
            'category_id' => $this->category->id,
            'name' => 'To Delete',
            'slug' => 'to-delete',
            'price' => 2.00,
            'is_available' => true,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/products/{$product->id}");

        $response->assertRedirect('/admin/products');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_product_validation_requires_name(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/products', [
            'category_id' => $this->category->id,
            'slug' => 'test-slug',
            'price' => 5.00,
            // Missing name
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_product_validation_requires_unique_slug(): void
    {
        Product::create([
            'business_id' => $this->business->id,
            'category_id' => $this->category->id,
            'name' => 'Existing',
            'slug' => 'existing-slug',
            'price' => 3.00,
            'is_available' => true,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($this->admin)->post('/admin/products', [
            'category_id' => $this->category->id,
            'name' => 'New Product',
            'slug' => 'existing-slug', // Duplicate
            'price' => 4.00,
        ]);

        $response->assertSessionHasErrors(['slug']);
    }
}

