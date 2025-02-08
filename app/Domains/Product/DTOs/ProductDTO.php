<?php

namespace App\Domains\Product\DTOs;

class ProductDTO
{
    public function __construct(
        public readonly string $name,
        public readonly float $price
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            price: $data['price']
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'price' => $this->price
        ];
    }
} 