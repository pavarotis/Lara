<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V2;

use App\Domain\Api\Models\ApiKey;
use App\Domain\Businesses\Models\Business;
use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class BusinessIsolationTest extends TestCase
{
    use RefreshDatabase;

    private function createBusiness(string $name, string $slug): Business
    {
        return Business::create([
            'name' => $name,
            'slug' => $slug,
            'type' => 'cafe',
            'is_active' => true,
        ]);
    }

    private function createApiKey(Business $business, array $scopes): array
    {
        $key = Str::random(32);
        $secret = Str::random(32);

        ApiKey::create([
            'business_id' => $business->id,
            'name' => "{$business->slug}-key",
            'key' => $key,
            'secret' => $secret, // Will be hashed by model boot
            'scopes' => $scopes,
            'is_active' => true,
        ]);

        return [$key, $secret];
    }

    public function test_products_are_scoped_to_api_key_business(): void
    {
        $businessA = $this->createBusiness('Cafe A', 'cafe-a');
        $businessB = $this->createBusiness('Cafe B', 'cafe-b');

        $categoryA = Category::create([
            'business_id' => $businessA->id,
            'name' => 'Coffee',
            'slug' => 'coffee',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $categoryB = Category::create([
            'business_id' => $businessB->id,
            'name' => 'Tea',
            'slug' => 'tea',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Product::create([
            'business_id' => $businessA->id,
            'category_id' => $categoryA->id,
            'name' => 'Espresso',
            'slug' => 'espresso',
            'price' => 2.50,
            'is_available' => true,
            'sort_order' => 1,
        ]);

        Product::create([
            'business_id' => $businessB->id,
            'category_id' => $categoryB->id,
            'name' => 'Green Tea',
            'slug' => 'green-tea',
            'price' => 3.00,
            'is_available' => true,
            'sort_order' => 1,
        ]);

        [$key, $secret] = $this->createApiKey($businessA, ['read:products']);

        $response = $this->withHeaders([
            'X-API-Key' => $key,
            'X-API-Secret' => $secret,
        ])->getJson('/api/v2/products');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['name' => 'Espresso']);
        $response->assertJsonMissing(['name' => 'Green Tea']);
    }

    public function test_product_show_cannot_access_other_business_by_id_or_slug(): void
    {
        $businessA = $this->createBusiness('Cafe A', 'cafe-a');
        $businessB = $this->createBusiness('Cafe B', 'cafe-b');

        $categoryA = Category::create([
            'business_id' => $businessA->id,
            'name' => 'Coffee',
            'slug' => 'coffee',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $categoryB = Category::create([
            'business_id' => $businessB->id,
            'name' => 'Tea',
            'slug' => 'tea',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $productA = Product::create([
            'business_id' => $businessA->id,
            'category_id' => $categoryA->id,
            'name' => 'Espresso',
            'slug' => 'espresso',
            'price' => 2.50,
            'is_available' => true,
            'sort_order' => 1,
        ]);

        $productB = Product::create([
            'business_id' => $businessB->id,
            'category_id' => $categoryB->id,
            'name' => 'Green Tea',
            'slug' => 'green-tea',
            'price' => 3.00,
            'is_available' => true,
            'sort_order' => 1,
        ]);

        [$key, $secret] = $this->createApiKey($businessA, ['read:products']);

        $headers = [
            'X-API-Key' => $key,
            'X-API-Secret' => $secret,
        ];

        $this->withHeaders($headers)
            ->getJson("/api/v2/products/{$productA->id}")
            ->assertStatus(200)
            ->assertJsonFragment(['name' => 'Espresso']);

        $this->withHeaders($headers)
            ->getJson("/api/v2/products/{$productB->id}")
            ->assertStatus(404);

        $this->withHeaders($headers)
            ->getJson('/api/v2/products/green-tea')
            ->assertStatus(404);
    }

    public function test_categories_are_scoped_to_api_key_business(): void
    {
        $businessA = $this->createBusiness('Cafe A', 'cafe-a');
        $businessB = $this->createBusiness('Cafe B', 'cafe-b');

        Category::create([
            'business_id' => $businessA->id,
            'name' => 'Coffee',
            'slug' => 'coffee',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Category::create([
            'business_id' => $businessB->id,
            'name' => 'Tea',
            'slug' => 'tea',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        [$key, $secret] = $this->createApiKey($businessA, ['read:categories']);

        $response = $this->withHeaders([
            'X-API-Key' => $key,
            'X-API-Secret' => $secret,
        ])->getJson('/api/v2/categories');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['name' => 'Coffee']);
        $response->assertJsonMissing(['name' => 'Tea']);
    }

    public function test_category_show_cannot_access_other_business_by_id_or_slug(): void
    {
        $businessA = $this->createBusiness('Cafe A', 'cafe-a');
        $businessB = $this->createBusiness('Cafe B', 'cafe-b');

        $categoryA = Category::create([
            'business_id' => $businessA->id,
            'name' => 'Coffee',
            'slug' => 'coffee',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $categoryB = Category::create([
            'business_id' => $businessB->id,
            'name' => 'Tea',
            'slug' => 'tea',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        [$key, $secret] = $this->createApiKey($businessA, ['read:categories']);

        $headers = [
            'X-API-Key' => $key,
            'X-API-Secret' => $secret,
        ];

        $this->withHeaders($headers)
            ->getJson("/api/v2/categories/{$categoryA->id}")
            ->assertStatus(200)
            ->assertJsonFragment(['name' => 'Coffee']);

        $this->withHeaders($headers)
            ->getJson("/api/v2/categories/{$categoryB->id}")
            ->assertStatus(404);

        $this->withHeaders($headers)
            ->getJson('/api/v2/categories/tea')
            ->assertStatus(404);
    }
}
