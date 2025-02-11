<?php

namespace Tests\Unit\Domains\Order\Services;

use App\Domains\Customer\Transforms\TransformOrder;
use App\Domains\Order\DTOs\OrderDTO;
use App\Domains\Order\Entities\Order;
use App\Domains\Order\Repositories\OrderRepository;
use App\Domains\Order\Services\OrderService;
use App\Domains\Product\Entities\Product;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery;

class TestableOrderService extends OrderService
{
    public function calculateTotal($order): float
    {
        return parent::calculateTotal($order);
    }
}

class OrderServiceTest extends MockeryTestCase
{
    private OrderService $service;
    private OrderRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(OrderRepository::class);
        
        $this->service = Mockery::mock(TestableOrderService::class, [$this->repository, new TransformOrder()])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }

    public function test_create_order(): void
    {
        // Arrange
        $order = [
            'customer_id' => 1,
            'products' => [
                [
                    'id' => 1,
                    'quantity' => 2,
                    'price' => 50
                ],
                [
                    'id' => 2,
                    'quantity' => 2,
                    'price' => 100
                ],
            ]
        ];

        $expectedOrder = [
            'customer_id' => 1,
            'total' => 300.0
        ];

        $dto = new OrderDTO(...$order);
        $orderModel = new Order($order);

        $this->repository->shouldReceive('create')
            ->once()
            ->with(['customer_id' => 1])
            ->andReturn($orderModel);

        $this->repository->shouldReceive('attachProducts')
            ->once()
            ->with($orderModel, $order['products'])
            ->andReturn($orderModel);

        $this->service->shouldReceive('sendEmail')
            ->once()
            ->with($orderModel)
            ->andReturn(null);

        $this->service->shouldReceive('calculateTotal')
            ->once()
            ->with($orderModel)
            ->andReturn(300);

        // Act
        $result = $this->service->create($dto);

        // Assert
        $this->service->shouldHaveReceived('sendEmail')->once();
        $this->assertEquals($expectedOrder, $result);
    }

    public function test_calculate_total(): void
    {
        // Arrange
        $products = collect([
            (object) [
                'id' => 1,
                'quantity' => 2,
                'price' => 50,
                'pivot' => (object) [
                    'quantity' => 2,
                    'price' => 50
                ]
            ],
            (object) [
                'id' => 2,
                'quantity' => 2,
                'price' => 100,
                'pivot' => (object) [
                    'quantity' => 2,
                    'price' => 100
                ]
            ]
        ]);

        $order = Mockery::mock(Order::class);
        $order->shouldReceive('getAttribute')
            ->with('products')
            ->andReturn($products);

        // Act
        $result = $this->service->calculateTotal($order);

        // Assert
        $expectedTotal = (2 * 50) + (2 * 100);
        $this->assertEquals($expectedTotal, $result);
    }

    public function test_calculate_total_with_empty_products(): void
    {
        // Arrange
        $products = collect([]);

        $order = Mockery::mock(Order::class);
        $order->shouldReceive('getAttribute')
            ->with('products')
            ->andReturn($products);

        // Act
        $result = $this->service->calculateTotal($order);

        // Assert
        $this->assertEquals(0, $result);
    }

    public function test_calculate_total_with_decimal_prices(): void
    {
        // Arrange
        $products = collect([
            (object) [
                'id' => 1,
                'quantity' => 2,
                'price' => 50.50,
                'pivot' => (object) [
                    'quantity' => 2,
                    'price' => 50.50
                ]
            ],
            (object) [
                'id' => 2,
                'quantity' => 3,
                'price' => 99.99,
                'pivot' => (object) [
                    'quantity' => 3,
                    'price' => 99.99
                ]
            ]
        ]);

        $order = Mockery::mock(Order::class);
        $order->shouldReceive('getAttribute')
            ->with('products')
            ->andReturn($products);

        // Act
        $result = $this->service->calculateTotal($order);

        // Assert
        $expectedTotal = (2 * 50.50) + (3 * 99.99);
        $this->assertEquals($expectedTotal, $result);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
