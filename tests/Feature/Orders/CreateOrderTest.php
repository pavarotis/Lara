<?php

declare(strict_types=1);

namespace Tests\Feature\Orders;

use App\Domain\Businesses\Models\Business;
use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Product;
use App\Domain\Orders\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateOrderTest extends TestCase
{
    use RefreshDatabase;

    private Business $business;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->business = Business::create([
            'name' => 'Test Cafe',
            'slug' => 'test-cafe',
            'type' => 'cafe',
            'settings' => [
                'delivery_enabled' => true,
                'pickup_enabled' => true,
            ],
            'is_active' => true,
        ]);

        $category = Category::create([
            'business_id' => $this->business->id,
            'name' => 'Coffee',
            'slug' => 'coffee',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->product = Product::create([
            'business_id' => $this->business->id,
            'category_id' => $category->id,
            'name' => 'Espresso',
            'slug' => 'espresso',
            'price' => 2.50,
            'is_available' => true,
            'sort_order' => 1,
        ]);
    }

    public function test_can_add_item_to_cart(): void
    {
        $response = $this->postJson('/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'cart_count' => 2,
        ]);
    }

    public function test_cart_page_shows_items(): void
    {
        // Add item to cart first
        $this->postJson('/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $response = $this->get('/cart');

        $response->assertStatus(200);
        $response->assertSee('Espresso');
    }

    public function test_checkout_requires_customer_info(): void
    {
        // Add item to cart
        $this->postJson('/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $response = $this->post('/checkout', [
            'type' => 'pickup',
            // Missing required fields
        ]);

        $response->assertSessionHasErrors(['customer_name', 'customer_phone']);
    }

    public function test_can_create_pickup_order(): void
    {
        // Add item to cart
        $this->postJson('/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $response = $this->post('/checkout', [
            'customer_name' => 'John Doe',
            'customer_phone' => '+30 6912345678',
            'customer_email' => 'john@example.com',
            'type' => 'pickup',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'type' => 'pickup',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('customers', [
            'name' => 'John Doe',
            'phone' => '+30 6912345678',
        ]);
    }

    public function test_delivery_order_requires_address(): void
    {
        // Add item to cart
        $this->postJson('/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $response = $this->post('/checkout', [
            'customer_name' => 'John Doe',
            'customer_phone' => '+30 6912345678',
            'type' => 'delivery',
            // Missing delivery_address
        ]);

        $response->assertSessionHasErrors(['delivery_address']);
    }

    public function test_can_create_delivery_order(): void
    {
        // Add item to cart
        $this->postJson('/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $response = $this->post('/checkout', [
            'customer_name' => 'Jane Doe',
            'customer_phone' => '+30 6987654321',
            'type' => 'delivery',
            'delivery_address' => 'Main Street 123, Athens',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'type' => 'delivery',
            'delivery_address' => 'Main Street 123, Athens',
        ]);
    }

    public function test_order_success_page_shows_details(): void
    {
        // Add item and create order
        $this->postJson('/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $this->post('/checkout', [
            'customer_name' => 'Test User',
            'customer_phone' => '+30 6900000000',
            'type' => 'pickup',
        ]);

        $order = Order::first();

        $response = $this->get("/checkout/success/{$order->order_number}");

        $response->assertStatus(200);
        $response->assertSee($order->order_number);
        $response->assertSee('Espresso');
    }

    public function test_empty_cart_redirects_from_checkout(): void
    {
        $response = $this->get('/checkout');

        $response->assertRedirect('/menu');
    }

    public function test_can_update_cart_quantity(): void
    {
        // Add item
        $this->postJson('/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        // Update quantity
        $response = $this->postJson('/cart/update', [
            'product_id' => $this->product->id,
            'quantity' => 5,
        ]);

        $response->assertJson([
            'success' => true,
            'cart_count' => 5,
        ]);
    }

    public function test_can_remove_item_from_cart(): void
    {
        // Add item
        $this->postJson('/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        // Remove
        $response = $this->postJson('/cart/remove', [
            'product_id' => $this->product->id,
        ]);

        $response->assertJson([
            'success' => true,
            'cart_count' => 0,
        ]);
    }

    public function test_can_clear_cart(): void
    {
        // Add item
        $this->postJson('/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 3,
        ]);

        // Clear
        $response = $this->postJson('/cart/clear');

        $response->assertJson([
            'success' => true,
            'cart_count' => 0,
        ]);
    }
}

