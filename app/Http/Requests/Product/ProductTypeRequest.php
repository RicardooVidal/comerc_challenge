<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductTypeRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
        ];

        // If it's an update request (PUT/PATCH), make fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['name'] = ['sometimes', 'string', 'max:255'];
            $rules['description'] = ['sometimes', 'string', 'max:255'];
        }

        return $rules;
    }
}
