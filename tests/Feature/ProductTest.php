<?php

namespace Tests\Feature;

use App\Domains\Product\Entities\Product;
use App\Domains\Product\Entities\ProductType;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->signIn();
    }

    public function test_can_list_products(): void
    {
        $this->withProductType();

        Product::factory(10)->create();

        $response = $this->getJson('/api/products');

        $response->assertOk();

        $response->assertJson(Product::all()->toArray());
    }

    public function test_can_show_product(): void
    {
        $this->withProductType();
        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertOk();

        $this->assertJson(json_encode($product->toArray()));
    }

    public function test_can_create_product_without_photo(): void
    {
        $this->withProductType();
        $product = Product::factory()->make();
        $productData = $product->toArray();

        $response = $this->postJson('/api/products', $productData);

        $response->assertCreated();

        $this->assertDatabaseHas('products', [
            'name' => $productData['name'],
            'price' => $productData['price'],
            'product_type_id' => $productData['product_type_id'],
            'photo' => null
        ]);
    }

    public function test_can_create_product_with_photo(): void
    {
        $this->withProductType();
        $product = Product::factory()->make();
        $productData = $product->toArray();
        $productData['photo'] = UploadedFile::fake()->image('test.png');

        $response = $this->postJson('/api/products', $productData);

        $response->assertCreated();

        $this->assertDatabaseHas('products', [
            'name' => $productData['name'],
            'price' => $productData['price'],
            'product_type_id' => $productData['product_type_id'],
            'photo' => 'produtos/' . $productData['photo']->hashName()
        ]);

        Storage::disk('public')->assertExists('produtos/' . $productData['photo']->hashName());
    }

    public function test_cannot_create_product_with_negative_price(): void
    {
        $response = $this->postJson('/api/products', [
            'name' => 'Test Product',
            'price' => -10,
            'product_type_id' => 1,
        ]);
    
        $response->assertStatus(422);
    }

    public function test_can_update_product_without_photo(): void
    {
        $this->withProductType();
        $product = Product::factory()->create();
       
        $productUpdated = [
            'name' => 'Updated Product',
            'price' => 100.0,
            'product_type_id' => 1
        ];

        $response = $this->putJson("/api/products/{$product->id}", $productUpdated);

        $response->assertOk();

        $this->assertDatabaseHas('products', [
            'name' => $productUpdated['name'],
            'price' => $productUpdated['price'],
            'product_type_id' => $productUpdated['product_type_id'],
            'photo' => null
        ]);
    }

    public function test_cannot_update_product_with_invalid_data(): void
    {
        $this->withProductType();
        $product = Product::factory()->create();
    
        $response = $this->putJson("/api/products/{$product->id}", [
            'name' => '',
            'price' => -10
        ]);
    
        $response->assertStatus(422);
    }

    public function test_can_update_product_with_photo_delete_old_photo(): void
    {
        $this->withProductType();
        $product = Product::factory()->create();
        $productData = $product->toArray();
        $productData['photo'] = UploadedFile::fake()->image('test.png');

        $productUpdated = [
            'name' => 'Updated Product',
            'price' => 100.0,
            'product_type_id' => 1,
            'photo' => UploadedFile::fake()->image('test.png')
        ];

        $this->postJson("/api/products/{$product->id}", $productData);
        $responsePut = $this->putJson("/api/products/{$product->id}", $productUpdated);

        $responsePut->assertOk();

        Storage::disk('public')->assertExists('produtos/' . $productUpdated['photo']->hashName());
        Storage::disk('public')->assertMissing('produtos/' . $productData['photo']->hashName());
    }

    public function test_can_delete_product(): void
    {
        $this->withProductType();
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertNoContent();

        $this->assertSoftDeleted('products', [
            'id' => $product->id
        ]);
    }

    public function test_cannot_delete_product_not_found(): void
    {
        $response = $this->deleteJson('/api/products/9999');
        $response->assertNotFound();
    }

    private function withProductType(): void
    {
        ProductType::create([
            'name' => 'Test Product Type'
        ]);
    }
}
