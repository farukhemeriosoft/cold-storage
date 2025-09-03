<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
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
        $customerId = $this->route('customer')->id;
        
        return [
            'full_name' => 'sometimes|required|string|max:255',
            'cnic_number' => 'sometimes|required|string|unique:customers,cnic_number,' . $customerId . '|regex:/^[0-9]{5}-[0-9]{7}-[0-9]{1}$/',
            'phone_number' => 'sometimes|required|string|max:20',
            'address' => 'sometimes|required|string|max:1000',
            'is_active' => 'sometimes|boolean',
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
            'is_active.boolean' => 'Active status must be true or false.',
        ];
    }
}
