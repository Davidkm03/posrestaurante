<x-layouts.app title="Nuevo Proveedor">
    <x-slot name="header">
        <div>
            <nav class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                <a href="{{ route('admin.suppliers.index') }}" class="hover:text-blue-600">Proveedores</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span>Nuevo Proveedor</span>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900">Nuevo Proveedor</h1>
        </div>
    </x-slot>

    <div class="max-w-3xl">
        <x-card>
            <form method="POST" action="{{ route('admin.suppliers.store') }}" class="space-y-6">
                @csrf

                <!-- Info básica -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Información Básica</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre/Razón Social *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0 focus:outline-none transition-colors"
                                placeholder="Nombre del proveedor">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="document_type" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Documento</label>
                            <select name="document_type" id="document_type"
                                class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0 focus:outline-none transition-colors">
                                <option value="31" {{ old('document_type') == '31' ? 'selected' : '' }}>NIT</option>
                                <option value="13" {{ old('document_type') == '13' ? 'selected' : '' }}>Cédula de Ciudadanía</option>
                                <option value="22" {{ old('document_type') == '22' ? 'selected' : '' }}>Cédula de Extranjería</option>
                                <option value="42" {{ old('document_type') == '42' ? 'selected' : '' }}>Pasaporte</option>
                            </select>
                        </div>

                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label for="document_number" class="block text-sm font-medium text-gray-700 mb-1">Número de Documento</label>
                                <input type="text" name="document_number" id="document_number" value="{{ old('document_number') }}"
                                    class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0 focus:outline-none transition-colors"
                                    placeholder="123456789">
                            </div>
                            <div class="w-16">
                                <label for="verification_digit" class="block text-sm font-medium text-gray-700 mb-1">DV</label>
                                <input type="text" name="verification_digit" id="verification_digit" value="{{ old('verification_digit') }}" maxlength="1"
                                    class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0 focus:outline-none transition-colors text-center"
                                    placeholder="0">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contacto -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Información de Contacto</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="contact_name" class="block text-sm font-medium text-gray-700 mb-1">Persona de Contacto</label>
                            <input type="text" name="contact_name" id="contact_name" value="{{ old('contact_name') }}"
                                class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0 focus:outline-none transition-colors"
                                placeholder="Nombre del contacto">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0 focus:outline-none transition-colors"
                                placeholder="proveedor@email.com">
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0 focus:outline-none transition-colors"
                                placeholder="(601) 123 4567">
                        </div>

                        <div>
                            <label for="mobile" class="block text-sm font-medium text-gray-700 mb-1">Celular</label>
                            <input type="text" name="mobile" id="mobile" value="{{ old('mobile') }}"
                                class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0 focus:outline-none transition-colors"
                                placeholder="300 123 4567">
                        </div>

                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                            <input type="text" name="address" id="address" value="{{ old('address') }}"
                                class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0 focus:outline-none transition-colors"
                                placeholder="Calle 123 # 45-67">
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
                            <input type="text" name="city" id="city" value="{{ old('city') }}"
                                class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0 focus:outline-none transition-colors"
                                placeholder="Bogotá">
                        </div>
                    </div>
                </div>

                <!-- Condiciones comerciales -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Condiciones Comerciales</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="credit_limit" class="block text-sm font-medium text-gray-700 mb-1">Límite de Crédito</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-gray-500">$</span>
                                <input type="number" name="credit_limit" id="credit_limit" value="{{ old('credit_limit', 0) }}" min="0" step="1000"
                                    class="w-full pl-8 pr-3 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0 focus:outline-none transition-colors">
                            </div>
                        </div>

                        <div>
                            <label for="payment_days" class="block text-sm font-medium text-gray-700 mb-1">Días de Pago</label>
                            <input type="number" name="payment_days" id="payment_days" value="{{ old('payment_days', 0) }}" min="0"
                                class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0 focus:outline-none transition-colors"
                                placeholder="30">
                        </div>

                        <div>
                            <label for="tax_regime" class="block text-sm font-medium text-gray-700 mb-1">Régimen Tributario</label>
                            <select name="tax_regime" id="tax_regime"
                                class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0 focus:outline-none transition-colors">
                                <option value="">Seleccionar...</option>
                                <option value="responsable_iva" {{ old('tax_regime') == 'responsable_iva' ? 'selected' : '' }}>Responsable de IVA</option>
                                <option value="no_responsable_iva" {{ old('tax_regime') == 'no_responsable_iva' ? 'selected' : '' }}>No Responsable de IVA</option>
                                <option value="regimen_simple" {{ old('tax_regime') == 'regimen_simple' ? 'selected' : '' }}>Régimen Simple</option>
                            </select>
                        </div>

                        <div>
                            <label class="flex items-center gap-3 mt-6">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">Proveedor Activo</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Notas -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                    <textarea name="notes" id="notes" rows="3"
                        class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0 focus:outline-none transition-colors resize-none"
                        placeholder="Observaciones adicionales...">{{ old('notes') }}</textarea>
                </div>

                <!-- Acciones -->
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="{{ route('admin.suppliers.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Guardar Proveedor
                    </button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>
