<?php

namespace App\Http\Requests;

use App\Enums\OrderType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('orders.create');
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(OrderType::class)],
            'table_id' => 'nullable|exists:tables,id',
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1|max:100',
            'items.*.notes' => 'nullable|string|max:255',
            'items.*.modifiers' => 'nullable|array',
            'items.*.modifiers.*' => 'exists:modifiers,id',
            'notes' => 'nullable|string|max:500',
            'guests' => 'nullable|integer|min:1|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'El tipo de orden es requerido',
            'items.required' => 'Debe agregar al menos un producto',
            'items.min' => 'Debe agregar al menos un producto',
            'items.*.product_id.required' => 'El producto es requerido',
            'items.*.product_id.exists' => 'El producto no existe',
            'items.*.quantity.required' => 'La cantidad es requerida',
            'items.*.quantity.min' => 'La cantidad mínima es 1',
        ];
    }
}
