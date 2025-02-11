<?php

namespace Tests\Unit\Domains\Order\Services;

use App\Domains\Customer\Entities\Customer;
use App\Domains\Customer\Transforms\TransformOrder;
use App\Domains\Order\DTOs\OrderDTO;
use App\Domains\Order\Entities\Order;
use App\Domains\Order\Repositories\OrderRepository;
use App\Domains\Order\Services\OrderService;
use App\Domains\Product\Entities\Product;
use App\Mail\OrderCreated;
use Illuminate\Support\Facades\Mail;
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

    public function test_find_order_by_id(): void
    {
        $order = Order::factory()->make()->toArray();
        $order['id'] = 1;

        $expectedOrder = [
            'id' => $order['id'],
            'customer_id' => 1,
            'total' => 100.0,
            'created_at' => null,
            'products' => collect([]),
            'customer_name' => null,
        ];

        $transformOrder = Mockery::mock(TransformOrder::class);
        $this->service = Mockery::mock(TestableOrderService::class, [$this->repository, $transformOrder])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        
        $orderModel = Mockery::mock(Order::class);
        $orderModel->shouldReceive('toArray')
            ->once()
            ->andReturn($order);
    
        $this->repository->shouldReceive('findById')
            ->once()
            ->with($order['id'])
            ->andReturn($orderModel);


        $this->service->shouldReceive('calculateTotal')
            ->once()
            ->with($orderModel)
            ->andReturn(100.0);

        $orderModel->shouldReceive('setAttribute')
            ->once()
            ->with('total', Mockery::type('float'));

        $transformOrder->shouldReceive('handle')
            ->once()
            ->with($order)
            ->andReturn($expectedOrder);

        $result = $this->service->findById($order['id']);
    
        $this->assertEquals($expectedOrder, $result);
    }

    public function test_find_all_orders(): void
    {
        $orders = Order::factory()
            ->count(2)
            ->make();

        $expectedOrders = [
            [
                'id' => 1,
                'customer_id' => 1,
                'total' => 100.0,
                'created_at' => null,
                'products' => null,
                'customer_name' => null,
            ],
            [
                'id' => 2,
                'customer_id' => 1,
                'total' => 100.0,
                'created_at' => null,
                'products' => null,
                'customer_name' => null,
            ],
        ];

        $transformOrder = Mockery::mock(TransformOrder::class);
        $this->service = Mockery::mock(TestableOrderService::class, [$this->repository, $transformOrder])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->repository->shouldReceive('findAll')
            ->once()
            ->with([])
            ->andReturn($orders);

        $this->service->shouldReceive('calculateTotal')
            ->once()
            ->with($orders[0])
            ->andReturn(100.0);

        $this->service->shouldReceive('calculateTotal')
            ->once()
            ->with($orders[1])
            ->andReturn(100.0);

        $transformOrder->shouldReceive('handle')
            ->twice()
            ->with(Mockery::on(function ($arg) use ($orders) {
                return $arg == $orders[0]->toArray() || $arg == $orders[1]->toArray();
            }))
            ->andReturnValues([$expectedOrders[0], $expectedOrders[1]]);

        $this->assertEquals($expectedOrders, $this->service->findAll([]));
    }

    public function test_create_order(): void
    {
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

        $result = $this->service->create($dto);

        $this->service->shouldHaveReceived('sendEmail')->once();
        $this->assertEquals($expectedOrder, $result);
    }

    public function test_calculate_total(): void
    {
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

        $result = $this->service->calculateTotal($order);

        $expectedTotal = (2 * 50) + (2 * 100);
        $this->assertEquals($expectedTotal, $result);
    }

    public function test_calculate_total_with_empty_products(): void
    {
        $products = collect([]);

        $order = Mockery::mock(Order::class);
        $order->shouldReceive('getAttribute')
            ->with('products')
            ->andReturn($products);

        $result = $this->service->calculateTotal($order);

        $this->assertEquals(0, $result);
    }

    public function test_calculate_total_with_decimal_prices(): void
    {
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

        $result = $this->service->calculateTotal($order);

        $expectedTotal = (2 * 50.50) + (3 * 99.99);
        $this->assertEquals($expectedTotal, $result);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
