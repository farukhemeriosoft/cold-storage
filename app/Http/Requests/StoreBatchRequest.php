<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|exists:customers,full_name',
            'unit_price' => 'required|numeric|min:0',
            'baskets' => 'required|array|min:1',
            'baskets.*.barcode' => 'required|string|distinct|unique:baskets,barcode',
        ];
    }
}


