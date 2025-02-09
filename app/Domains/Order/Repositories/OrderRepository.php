<?php

namespace App\Domains\Order\Repositories;

use App\Repositories\BaseRepositoryInterface;
use App\Domains\Order\Entities\Order;
use Illuminate\Support\Collection;

class OrderRepository implements BaseRepositoryInterface
{
    public function __construct(
        private readonly Order $order
    )
    {}

    public function create(array $data): Order
    {
        return $this->order->create($data);
    }
    
    public function findById(int $id): ?Order
    {
        return $this->order
            ->with('products', 'customer')
            ->findOrFail($id);
    }
    
    public function update(int $id, array $data): bool
    {
        $order = $this->order->findOrFail($id);
        $this->syncProducts($order, $data['products']);

        return $order->update($data);
    }
    
    public function delete(int $id): void
    {
        $order = $this->findById($id);
        $order->products()->newPivotStatement()->where('order_id', $id)->update(['deleted_at' => now()]);
        $order->delete();
    }
    
    public function findAll(array $filters): Collection
    {
        return $this->order
            ->with('products', 'customer')
            ->where($filters)->get();
    }

    public function syncProducts(Order $order, array $products): void
    {
        // Soft delete existing relationships
        $order->products()->newPivotStatement()->where('order_id', $order->id)->update(['deleted_at' => now()]);
        
        foreach ($products as $product) {
            $order->products()->attach($product['product_id'], [
                'quantity' => $product['quantity'],
                'price' => $product['price'],
                'deleted_at' => null
            ]);
        }
    }

    /**
     * Attach products to order table pivot
     */
    public function attachProducts(Order $order, array $products): Order
    {
        foreach ($products as $product) {
            $order->products()->attach($product['product_id'], [
                'quantity' => $product['quantity'],
                'price' => $product['price']
            ]);
        }

        return $this->findById($order->id);
    }
}
