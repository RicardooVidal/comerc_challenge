<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class AllProductTypeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['integer'],
            'name' => ['string'],
            'description' => ['string']
        ];
    }
}
