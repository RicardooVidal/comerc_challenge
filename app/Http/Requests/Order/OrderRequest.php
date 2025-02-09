<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'products' => ['required', 'array'],
            'products.*.product_id' => ['required', 'exists:products,id,deleted_at,NULL', 'distinct'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
            'products.*.price' => ['required', 'numeric', 'min:1']
        ];
    }
}
