<?php

namespace Database\Factories\Domains\Order\Entities;

use App\Domains\Order\Entities\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'customer_id' => $this->faker->numberBetween(1, 10)
        ];  
    }
}