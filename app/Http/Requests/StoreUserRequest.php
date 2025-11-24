<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('users.create');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'pin' => 'required|string|size:4',
            'position' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'role' => ['required', Rule::enum(UserRole::class)],
            'branches' => 'required|array|min:1',
            'branches.*' => 'exists:branches,id',
            'default_branch' => 'required|exists:branches,id',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es requerido',
            'email.required' => 'El email es requerido',
            'email.email' => 'El email no es válido',
            'email.unique' => 'Este email ya está registrado',
            'password.required' => 'La contraseña es requerida',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'pin.required' => 'El PIN es requerido',
            'pin.size' => 'El PIN debe tener 4 dígitos',
            'role.required' => 'Debe seleccionar un rol',
            'branches.required' => 'Debe seleccionar al menos una sucursal',
            'branches.min' => 'Debe seleccionar al menos una sucursal',
            'default_branch.required' => 'Debe seleccionar una sucursal principal',
        ];
    }
}
