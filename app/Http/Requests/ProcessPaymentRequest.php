<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('payments.process');
    }

    public function rules(): array
    {
        return [
            'payments' => 'required|array|min:1',
            'payments.*.payment_method_id' => 'required|exists:payment_methods,id',
            'payments.*.amount' => 'required|numeric|min:0.01',
            'payments.*.reference' => 'nullable|string|max:100',
            'tip' => 'nullable|numeric|min:0',
            'generate_invoice' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'payments.required' => 'Debe seleccionar al menos un método de pago',
            'payments.*.payment_method_id.required' => 'El método de pago es requerido',
            'payments.*.payment_method_id.exists' => 'Método de pago no válido',
            'payments.*.amount.required' => 'El monto es requerido',
            'payments.*.amount.min' => 'El monto debe ser mayor a 0',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $order = $this->route('order');
            $totalPayment = collect($this->payments)->sum('amount');
            $tip = $this->tip ?? 0;
            $required = $order->total + $tip;

            if ($totalPayment < $required) {
                $validator->errors()->add('payments', "Pago insuficiente. Requerido: \${$required}, Recibido: \${$totalPayment}");
            }
        });
    }
}
