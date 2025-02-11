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
    ) {
    }

    public function findById(int $id): array
    {
        $order = $this->orderRepository->findById($id);
        $order->total = $this->calculateTotal($order);
        return $this->transformOrder->handle($order->toArray());
    }

    public function findAll(array $filters): array
    {
        $orders = $this->orderRepository->findAll($filters);

        return $orders->map(function ($order) {
            $order->total = $this->calculateTotal($order);
            return $this->transformOrder->handle($order->toArray());
        })->toArray();
    }

    public function create(OrderDTO $dto): array
    {
        $order = $this->orderRepository->create([
            'customer_id' => $dto->customer_id,
        ]);

        $order = $this->orderRepository->attachProducts($order, $dto->products);

        $this->sendEmail($order);

        $order->total = $this->calculateTotal($order);

        return $order->toArray();
    }

    public function update(int $id, OrderDTO $dto): ?bool
    {
        $order = $this->orderRepository->findById($id);

        return $this->orderRepository->update($id, $dto->toArray());
    }

    public function delete(int $id): void
    {
        $this->orderRepository->delete($id);
    }

    /**
     * Calculate total of order
     */
    protected function calculateTotal(Order $order): float
    {
        return $order->products->sum(function ($product) {
            return $product->pivot->quantity * $product->pivot->price;
        });
    }

    /**
     * Send email to customer
     */
    protected function sendEmail(Order $order): void
    {
        try {
            Mail::to($order->customer->email)->send(new OrderCreated($order));
        } catch (\Exception $e) {
            Log::error('Failed to send order confirmation email', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
