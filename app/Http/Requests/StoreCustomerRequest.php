<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'cnic_number' => 'required|string|unique:customers,cnic_number|regex:/^[0-9]{5}-[0-9]{7}-[0-9]{1}$/',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'full_name.required' => 'Full name is required.',
            'cnic_number.required' => 'CNIC number is required.',
            'cnic_number.unique' => 'This CNIC number is already registered.',
            'cnic_number.regex' => 'CNIC number must be in format: 12345-1234567-1',
            'phone_number.required' => 'Phone number is required.',
            'address.required' => 'Address is required.',
        ];
    }
}
