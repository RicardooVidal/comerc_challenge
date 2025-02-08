<?php

namespace App\Domains\Customer\Transforms;

class TransformOrder
{
    public function handle(array $data): array
    {
        return [
            'id' => $data['id'],
            'customer_id' => $data['customer_id'],
            'customer_name' => $data['customer']['name'],
            'total' => $data['total'],
            'created_at' => $data['created_at'],
            'products' => $data['products'],
        ];
    }
}
