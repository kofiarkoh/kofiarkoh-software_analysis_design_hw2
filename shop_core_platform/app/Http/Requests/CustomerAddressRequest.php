<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerAddressRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'additional_phone_number' => 'nullable|string|max:20',
            'address' => 'required|string|max:500',
            'additional_info' => 'nullable|string|max:1000',
            'region' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'is_default' => 'nullable|boolean',
        ];
    }
}
