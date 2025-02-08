<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    public function rules(): array
    {
        $customerId = $this->route('customer');

        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:customers,email,' . $customerId],
            'phone' => ['required', 'string'],
            'birth_date' =>  ['required', 'date:Y-m-d'],
            'address' => ['required', 'string'],
            'address_complement' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'state' => ['nullable', 'string', 'size:2'],
            'neighborhood' => ['required', 'string'],
            'zip_code' => ['required', 'string']
        ];
    }
}
