<?php

namespace Database\Factories\Domains\Customer\Entities;

use App\Domains\Customer\Entities\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('###########'),
            'birth_date' => fake()->date(),
            'address' => fake()->streetAddress(),
            'address_complement' => fake()->optional()->secondaryAddress(),
            'neighborhood' => fake()->word(),
            'city' => fake()->city(),
            'state' => fake()->lexify('??'),
            'zip_code' => fake()->numerify('########')
        ];
    }
}