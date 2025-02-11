<?php

namespace Tests\Feature;

use App\Domains\Customer\Entities\Customer;
use App\Domains\Order\Entities\Order;
use App\Domains\Order\Repositories\OrderRepository;
use App\Domains\Product\Entities\Product;
use App\Domains\Product\Entities\ProductType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_orders(): void
    {
        // Arrange
        $this->withCustomers();
        Order::factory(10)->create();

        // Act
        $response = $this->getJson('/api/orders');

        // Assert
        $response->assertOk();

        $response->assertJson(Order::all()->toArray());
    }

    public function test_can_show_order(): void
    {
        // Arrange
        $this->withCustomers();
        $order = $this->withOrder();

        // Act
        $response = $this->getJson("/api/orders/{$order->id}");

        // Assert
        $response->assertOk();

        $this->assertJson(json_encode($order->toArray()));
    }

    public function test_can_create_order(): void
    {
        // Arrange
        $this->withCustomers();
        $this->withProductType();
        $this->withProducts();

        $orderData = Order::factory()->make();
        $orderData['products'] = $this->withProducts()->pluck('id')->map(
            fn ($id) => ['product_id' => $id, 'quantity' => 1, 'price' => 1]
        )->toArray();

        // Act
        $response = $this->postJson('/api/orders', $orderData->toArray());

        // Assert
        $response->assertCreated();

        $orderId = $response->json('id');

        $this->assertDatabaseHas('orders', [
            'customer_id' => $orderData['customer_id']
        ]);

        $this->assertDatabaseHas('order_product', [
            'order_id' => $orderId,
            'product_id' => $orderData['products'][0]['product_id'],
            'quantity' => $orderData['products'][0]['quantity'],
            'price' => $orderData['products'][0]['price']
        ]);
    }

    public function test_cannot_create_order_with_invalid_customer(): void
    {
        $response = $this->postJson('/api/orders', [
            'customer_id' => 9999,
            'products' => [
                ['product_id' => 1, 'quantity' => 1, 'price' => 1]
            ]
        ]);
    
        $response->assertStatus(422);
    }

    public function test_can_update_order(): void
    {
        // Arrange
        $this->withCustomers();
        $this->withProductType();

        $order = $this->withOrder();        
        $orderData = Order::factory()->make();
        $orderData['products'] = $this->withProducts()->pluck('id')->map(
            fn ($id) => ['product_id' => $id, 'quantity' => 1, 'price' => 1]
        )->toArray();

        // Act
        $response = $this->putJson("/api/orders/{$order->id}", $orderData->toArray());

        // Assert
        $response->assertOk();

        $orderId = $order->id;

        $this->assertDatabaseHas('orders', [
            'customer_id' => $orderData['customer_id']
        ]);

        $this->assertDatabaseHas('order_product', [
            'order_id' => $orderId,
            'product_id' => $orderData['products'][0]['product_id'],
            'quantity' => $orderData['products'][0]['quantity'],
            'price' => $orderData['products'][0]['price']
        ]);
        
    }

    public function test_cannot_update_order_with_invalid_data(): void
    {
        $this->withCustomers();

        $order = Order::factory()->create();
    
        $response = $this->putJson("/api/orders/{$order->id}", [
            'customer_id' => 9999,

        ]);
    
        $response->assertStatus(422);
    }

    public function test_can_delete_order(): void
    {
        // Arrange
        $this->withCustomers();
        $this->withProductType();

        $order = $this->withOrder();
        DB::table('order_product')->insert([
            'order_id' => $order->id,
            'product_id' => Product::factory()->create()->id,
            'quantity' => 1,
            'price' => 1
        ]);

        // Act
        $response = $this->deleteJson("/api/orders/{$order->id}");

        // Assert
        $response->assertNoContent();

        $this->assertSoftDeleted('orders', [
            'id' => $order->id
        ]);

        $this->assertSoftDeleted('order_product', [
            'order_id' => $order->id
        ]);
    }

    public function test_cannot_delete_nonexistent_order(): void
    {
        $response = $this->deleteJson('/api/orders/9999');
        $response->assertNotFound();
    }

    private function withOrder(): Order
    {
        return Order::factory()->create();
    }

    private function withProducts(): Collection
    {
        return Product::factory(10)->create();
    }

    private function withCustomers(): Collection
    {
        return Customer::factory(10)->create();
    }

    private function withProductType(): void
    {
        ProductType::create([
            'name' => 'Test Product Type'
        ]);
    }
}
