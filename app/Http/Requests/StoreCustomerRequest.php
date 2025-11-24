<?php

namespace App\Http\Requests;

use App\Enums\CustomerType;
use App\Enums\DocumentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('customers.create');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'customer_type' => ['required', Rule::enum(CustomerType::class)],
            'document_type' => ['required', Rule::enum(DocumentType::class)],
            'document_number' => 'required|string|max:20|unique:customers,document_number',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'business_name' => 'nullable|required_if:customer_type,business|string|max:255',
            'tax_regime' => 'nullable|string|max:100',
            'tax_responsibilities' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es requerido',
            'document_type.required' => 'El tipo de documento es requerido',
            'document_number.required' => 'El número de documento es requerido',
            'document_number.unique' => 'Este número de documento ya está registrado',
            'email.email' => 'El email no es válido',
            'business_name.required_if' => 'El nombre comercial es requerido para empresas',
        ];
    }
}
