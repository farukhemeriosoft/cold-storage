<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBatchRequest extends FormRequest
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
            'room_id' => 'required|integer|exists:rooms,id',
            'floor_id' => 'required|integer|exists:floors,id',
            'zone_id' => 'required|integer|exists:zones,id',
        ];
    }
}
