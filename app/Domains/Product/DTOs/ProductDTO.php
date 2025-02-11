<?php

namespace App\Domains\Product\DTOs;

class ProductDTO
{
    public function __construct(
        public readonly string $name,
        public readonly float $price,
        public readonly int $product_type_id
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            price: $data['price'],
            product_type_id: $data['product_type_id']
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'price' => $this->price,
            'product_type_id' => $this->product_type_id
        ];
    }
}
