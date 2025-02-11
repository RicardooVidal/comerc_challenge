<?php

namespace App\Domains\Customer\Transforms;

class TransformOrder
{
    public function handle(array $data): array
    {
        return [
            'id' => $data['id'],
            'customer_id' => $data['customer_id'],
            'customer_name' => $data['customer']['name'] ?? null,
            'total' => $data['total'] ?? 0,
            'created_at' => $data['created_at'],
            'products' => $data['products'],
        ];
    }
}
