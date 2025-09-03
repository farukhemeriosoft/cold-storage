<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBasketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|integer|exists:customers,id',
            'unit_price' => 'required|numeric|min:0',
            'barcode' => 'required|string|max:255|unique:baskets,barcode',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Customer ID is required.',
            'customer_id.exists' => 'Customer not found.',
            'barcode.required' => 'Barcode is mandatory.',
            'barcode.unique' => 'This barcode already exists.',
        ];
    }
}


