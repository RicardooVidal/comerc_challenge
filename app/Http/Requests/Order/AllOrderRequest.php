<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class AllOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['integer'],
            'customer_id' => ['integer'],
            'product_id' => ['integer'],
            'date' => ['date:Y-m-d'],
        ];
    }
}
