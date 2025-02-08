<?php

namespace App\Domains\Order\DTOs;

class OrderDTO
{
    public function __construct(
        public readonly int $customer_id,
        public readonly array $products
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            customer_id: $data['customer_id'],
            products: $data['products']
        );
    }

    public function toArray(): array
    {
        return [
            'customer_id' => $this->customer_id,
            'products' => $this->products
        ];
    }
} 