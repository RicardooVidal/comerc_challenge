<?php

namespace Tests\Feature;

use App\Domains\Product\Entities\Product;
use App\Domains\Product\Entities\ProductType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_products(): void
    {
        $this->withProductType();

        // Arrange
        Product::factory(10)->create();

        // Act
        $response = $this->getJson('/api/products');

        // Assert
        $response->assertOk();

        $response->assertJson(Product::all()->toArray());
    }

    public function test_can_show_product(): void
    {
        // Arrange
        $this->withProductType();
        $product = Product::factory()->create();

        // Act
        $response = $this->getJson("/api/products/{$product->id}");

        // Assert
        $response->assertOk();

        $this->assertJson(json_encode($product->toArray()));
    }

    public function test_can_create_product_without_photo(): void
    {
        // Arrange
        $this->withProductType();
        $product = Product::factory()->make();
        $productData = $product->toArray();

        // Act
        $response = $this->postJson('/api/products', $productData);

        // Assert
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
        // Arrange
        $this->withProductType();
        $product = Product::factory()->make();
        $productData = $product->toArray();
        $productData['photo'] = UploadedFile::fake()->image('test.jpg');

        // Act
        $response = $this->postJson('/api/products', $productData);

        // Assert
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
        // Arrange
        $this->withProductType();
        $product = Product::factory()->create();
       
        $productUpdated = [
            'name' => 'Updated Product',
            'price' => 100.0,
            'product_type_id' => 1
        ];

        // Act
        $response = $this->putJson("/api/products/{$product->id}", $productUpdated);

        // Assert
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
        // Arrange
        $this->withProductType();
        $product = Product::factory()->create();
        $productData = $product->toArray();
        $productData['photo'] = UploadedFile::fake()->image('test.jpg');

        $productUpdated = [
            'name' => 'Updated Product',
            'price' => 100.0,
            'product_type_id' => 1,
            'photo' => UploadedFile::fake()->image('test.jpg')
        ];

        // Act
        $this->postJson("/api/products/{$product->id}", $productData);
        $responsePut = $this->putJson("/api/products/{$product->id}", $productUpdated);

        // Assert
        $responsePut->assertOk();

        Storage::disk('public')->assertExists('produtos/' . $productUpdated['photo']->hashName());
        Storage::disk('public')->assertMissing('produtos/' . $productData['photo']->hashName());
    }

    public function test_can_delete_product(): void
    {
        // Arrange
        $this->withProductType();
        $product = Product::factory()->create();

        // Act
        $response = $this->deleteJson("/api/products/{$product->id}");

        // Assert
        $response->assertNoContent();

        $this->assertSoftDeleted('products', [
            'id' => $product->id
        ]);
    }

    public function test_cannot_delete_nonexistent_product(): void
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
