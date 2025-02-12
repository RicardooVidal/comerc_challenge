<?php

namespace App\Tests\Unit\Domains\Order\Repositories;

use App\Domains\Order\Entities\Order;
use App\Domains\Order\Repositories\OrderRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class OrderRepositoryTest extends MockeryTestCase
{
    private OrderRepository $repository; 
    private Order $order;

    public function setUp(): void
    {
        $this->order = Mockery::mock(Order::class);
        $this->repository = new OrderRepository($this->order);
    }

    public function test_find_order_by_id(): void
    {
        $order = Order::factory()->make()->toArray();
        $order['id'] = 1;

        $this->order->shouldReceive('with')
            ->once()
            ->with('products', 'customer')
            ->andReturnSelf($this->order);

        $this->order->shouldReceive('findOrFail')
            ->once()
            ->with($order['id'])
            ->andReturn($this->order);

        $this->repository->findById($order['id']);
    }

    public function test_find_order_by_id_not_found(): void
    {
        $id = 1;

        $this->order->shouldReceive('with')
            ->once()
            ->with('products', 'customer')
            ->andReturnSelf($this->order);

        $this->order->shouldReceive('findOrFail')
            ->once()
            ->with($id)
            ->andThrow(new ModelNotFoundException());

        $this->expectException(ModelNotFoundException::class);

        $this->repository->findById($id);
    }

    public function test_find_all_orders(): void
    {
        $this->order->shouldReceive('with')
            ->twice()
            ->with('products', 'customer')
            ->andReturnSelf($this->order);

        $this->order->shouldReceive('where')
            ->twice()
            ->with([])
            ->andReturnSelf($this->order);

        $this->order->shouldReceive('get')
            ->once()
            ->andReturn(collect([]));

        $this->repository->findAll([]);

        $this->order->shouldReceive('get')
            ->once()
            ->andReturn(collect([]));

        $this->repository->findAll([]);
    }

    public function test_create_order(): void
    {
        $order = Order::factory()->make()->toArray();
        $order['id'] = 1;

        $this->order->shouldReceive('create')
            ->once()
            ->with($order)
            ->andReturn($this->order);

        $this->repository->create($order);
    }

    public function test_update_order(): void
    {
        $this->repository = Mockery::mock(OrderRepository::class, [$this->order])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $orderData = [
            'id' => 1,
            'customer_id' => 1,
            'products' => [
                [
                    'product_id' => 1,
                    'quantity' => 2,
                    'price' => 50.00
                ]
            ]
        ];

        $this->order->shouldReceive('findOrFail')
            ->once()
            ->with($orderData['id'])
            ->andReturn($this->order);

        $this->repository->shouldReceive('syncProducts')
            ->once()
            ->with($this->order, $orderData['products']);

        $this->order->shouldReceive('update')
            ->once()
            ->with($orderData)
            ->andReturn(true);

        $this->repository->update($orderData['id'], $orderData);

        $this->repository->shouldHaveReceived('syncProducts')->once();
        $this->repository->shouldHaveReceived('update')->once();
    }

    public function test_update_order_not_found(): void
    {
        $this->repository = Mockery::mock(OrderRepository::class, [$this->order])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $orderData = [
            'id' => 1,
            'customer_id' => 1,
            'products' => [
                [
                    'product_id' => 1,
                    'quantity' => 2,
                    'price' => 50.00
                ]
            ]
        ];

        $this->order->shouldReceive('findOrFail')
            ->once()
            ->with($orderData['id'])
            ->andThrow(ModelNotFoundException::class);

        $this->expectException(ModelNotFoundException::class);

        $this->repository->update($orderData['id'], $orderData);

        $this->order->shouldHaveReceived('findOrFail')->once();
        $this->order->shouldHaveReceived('update')->never();
    }

    public function test_delete_order(): void
    {
        $deletedAt = Carbon::now();
        Carbon::setTestNow($deletedAt);

        $order = Order::factory()->make()->toArray();
        $order['id'] = 1;

        $this->order->shouldReceive('findOrFail')
            ->once()
            ->with($order['id'])
            ->andReturn($this->order);

        $this->order->shouldReceive('with')
            ->once()
            ->with('products', 'customer')
            ->andReturnSelf();

        $productsMock = Mockery::mock(BelongsToMany::class);

        $this->order->shouldReceive('products')
            ->once()
            ->andReturn($productsMock);

        $builder = Mockery::mock(Builder::class);

        $productsMock->shouldReceive('newPivotStatement')
            ->once()
            ->andReturn($builder);

        $builder->shouldReceive('where')
            ->once()
            ->with('order_id', $order['id'])
            ->andReturnSelf();

        $builder->shouldReceive('update')
            ->once()
            ->with(['deleted_at' => $deletedAt])
            ->andReturn(1);

        $this->order->shouldReceive('delete')
            ->once()
            ->andReturn(true);
    
        $this->repository->delete($order['id']);
    
        $this->order->shouldHaveReceived('delete')->once();
        $this->order->shouldHaveReceived('products')->once();
        $this->order->shouldHaveReceived('with')->once();
        $productsMock->shouldHaveReceived('newPivotStatement')->once();
        $builder->shouldHaveReceived('where')->once();
        $builder->shouldHaveReceived('update')->once();
        $this->order->shouldHaveReceived('delete')->once();
    }

    public function test_delete_order_not_found(): void
    {
        $id = 1;

        $this->order->shouldReceive('findOrFail')
            ->once()
            ->with($id)
            ->andThrow(ModelNotFoundException::class);

        $this->order->shouldReceive('with')
            ->once()
            ->with('products', 'customer')
            ->andReturnSelf();

        $this->expectException(ModelNotFoundException::class);

        $this->repository->delete($id);

        $this->order->shouldHaveReceived('findOrFail')->once();
        $this->order->shouldHaveReceived('delete')->never();
    }
}
