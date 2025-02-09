<?php

namespace App\Domains\Product\DTOs;

class ProductTypeDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $description
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description']
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description
        ];
    }
} 