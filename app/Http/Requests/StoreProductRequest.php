<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('products.create');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|max:50|unique:products,sku',
            'barcode' => 'nullable|string|max:50|unique:products,barcode',
            'tax_type' => 'required|string|in:01,04,ZZ',
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'tax_included' => 'boolean',
            'is_active' => 'boolean',
            'show_in_pos' => 'boolean',
            'allow_notes' => 'boolean',
            'image' => 'nullable|image|max:2048',
            'modifier_groups' => 'nullable|array',
            'modifier_groups.*' => 'exists:modifier_groups,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del producto es requerido',
            'category_id.required' => 'Debe seleccionar una categoría',
            'category_id.exists' => 'La categoría seleccionada no existe',
            'price.required' => 'El precio es requerido',
            'price.numeric' => 'El precio debe ser un número',
            'price.min' => 'El precio no puede ser negativo',
            'sku.unique' => 'Este SKU ya está en uso',
            'barcode.unique' => 'Este código de barras ya está en uso',
            'image.image' => 'El archivo debe ser una imagen',
            'image.max' => 'La imagen no debe superar 2MB',
        ];
    }
}
