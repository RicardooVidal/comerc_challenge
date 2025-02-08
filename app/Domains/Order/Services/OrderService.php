<?php

namespace App\Domains\Order\Services;

use App\Domains\Customer\Transforms\TransformOrder;
use App\Domains\Order\DTOs\OrderDTO;
use App\Domains\Order\Entities\Order;
use App\Domains\Order\Repositories\OrderRepository;
use App\Mail\OrderCreated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderService
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly TransformOrder $transformOrder
    )
    {}
    
    public function create(OrderDTO $dto): array
    {
        $order = $this->orderRepository->create([
            'customer_id' => $dto->customer_id,
        ]);

        // Attach product to order table pivot
        $this->attachProducts($order, $dto->products);

        // Send email
        try {
            Mail::to($order->customer->email)->send(new OrderCreated($order));
        } catch (\Exception $e) {
            // Log error but don't stop the process
            Log::error('Failed to send order confirmation email', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }

        $order->total = $this->calculateTotal($order);

        return $this->transformOrder->handle($order->toArray());
    }
    
    public function findById(int $id): array
    {
        $order = $this->orderRepository->findById($id);
        $order->total = $this->calculateTotal($order);

        return $this->transformOrder->handle($order->toArray());
    }
    
    public function update(int $id, OrderDTO $dto): bool
    {
        $order = $this->orderRepository->findById($id);
        $this->orderRepository->syncProducts($order, $dto->products);
        
        return $this->orderRepository->update($id, $dto->toArray());
    }
    
    public function delete(int $id): void
    {
        $this->orderRepository->delete($id);
    }

    public function findAll(array $filters): array
    {
        $orders = $this->orderRepository->findAll($filters);

        return $orders->map(function ($order) {
            $order->total = $this->calculateTotal($order);
            return $this->transformOrder->handle($order->toArray());
        })->toArray();
    }

    /**
     * Attach products to order table pivot
     */
    private function attachProducts(Order $order, array $products): void
    {
        foreach ($products as $product) {
            $order->products()->attach($product['product_id'], [
                'quantity' => $product['quantity'],
                'price' => $product['price']
            ]);
        }
    }

    /**
     * Calculate total of order
     */
    private function calculateTotal(Order $order): float
    {
        return $order->products->sum(function ($product) {
            return $product->pivot->quantity * $product->pivot->price;
        });
    }
}
