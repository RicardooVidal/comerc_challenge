<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class AllCustomerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['integer'],
            'name' => ['string'],
            'email' => ['email']
        ];
    }
}
