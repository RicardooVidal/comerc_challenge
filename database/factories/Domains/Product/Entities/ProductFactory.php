<?php

namespace Database\Factories\Domains\Product\Entities;

use App\Domains\Product\Entities\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'price' => fake()->numberBetween(1, 100),
            'product_type_id' => 1
        ];
    }
}