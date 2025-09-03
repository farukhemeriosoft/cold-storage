<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddBasketsToBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'batch_id' => 'required|integer|exists:batches,id',
            'baskets' => 'required|array|min:1',
            'baskets.*.barcode' => 'required|string|distinct|unique:baskets,barcode',
        ];
    }
}
