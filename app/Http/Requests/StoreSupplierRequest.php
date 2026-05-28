<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_code' => ['nullable', 'string', 'max:50', 'unique:suppliers,supplier_code'],
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'address' => ['nullable', 'string'],
            'province' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'barangay' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'street_address' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'is_blacklisted' => ['nullable', 'boolean'],
            'blacklist_reason' => ['nullable', 'string', 'max:1000', 'required_if:is_blacklisted,1'],
            'contacts' => ['nullable', 'array'],
            'contacts.*.name' => ['nullable', 'string', 'max:255'],
            'contacts.*.role' => ['nullable', 'string', 'max:255'],
            'contacts.*.phone' => ['nullable', 'string', 'max:50'],
            'contacts.*.email' => ['nullable', 'email', 'max:255'],
            'contacts.*.notes' => ['nullable', 'string', 'max:500'],
            'contacts.*.is_primary' => ['nullable', 'boolean'],
            'create_account' => ['nullable', 'boolean'],
            'account_name' => ['nullable', 'string', 'max:255', 'required_if:create_account,1'],
            'account_email' => ['nullable', 'email', 'max:255', 'required_if:create_account,1', 'unique:users,email'],
            'account_password' => ['nullable', 'string', 'min:8', 'required_if:create_account,1'],
        ];
    }
}