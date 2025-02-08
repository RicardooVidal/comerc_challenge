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
        return $this->order->findOrFail($id)->update($data);
    }
    
    public function delete(int $id): void
    {
        $order = $this->findById($id);
        $order->products()->detach();
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
        $order->products()->detach();
        
        foreach ($products as $product) {
            $order->products()->attach($product['product_id'], [
                'quantity' => $product['quantity'],
                'price' => $product['price']
            ]);
        }
    }
}
