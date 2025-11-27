@extends('layouts.admin')

@section('title', 'Nueva Promoción')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.promotions.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Basic Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Información Básica</h3>
            
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre de la Promoción *
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name') }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                           placeholder="Ej: Happy Hour 2x1">
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Descripción
                    </label>
                    <textarea name="description" 
                              id="description" 
                              rows="2"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Descripción visible para el personal">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="coupon_code" class="block text-sm font-medium text-gray-700 mb-1">
                            Código de Cupón
                        </label>
                        <input type="text" 
                               name="coupon_code" 
                               id="coupon_code" 
                               value="{{ old('coupon_code') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 uppercase font-mono"
                               placeholder="Ej: DESCUENTO20">
                        <p class="mt-1 text-xs text-gray-500">Dejar vacío para promociones automáticas</p>
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">
                            Prioridad
                        </label>
                        <input type="number" 
                               name="priority" 
                               id="priority" 
                               value="{{ old('priority', 0) }}"
                               min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Mayor número = mayor prioridad</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Discount Type -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tipo de Descuento</h3>
            
            <div class="space-y-4">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                        Tipo de Promoción *
                    </label>
                    <select name="type" 
                            id="type" 
                            required
                            onchange="toggleTypeFields()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>Porcentaje de Descuento</option>
                        <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Monto Fijo</option>
                        <option value="buy_x_get_y" {{ old('type') === 'buy_x_get_y' ? 'selected' : '' }}>Compra X Lleva Y</option>
                        <option value="happy_hour" {{ old('type') === 'happy_hour' ? 'selected' : '' }}>Happy Hour</option>
                        <option value="free_delivery" {{ old('type') === 'free_delivery' ? 'selected' : '' }}>Delivery Gratis</option>
                    </select>
                </div>

                <!-- Percentage/Fixed Fields -->
                <div id="discountValueFields" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="discount_value" class="block text-sm font-medium text-gray-700 mb-1">
                            <span id="discountLabel">Porcentaje</span> *
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   name="discount_value" 
                                   id="discount_value" 
                                   value="{{ old('discount_value') }}"
                                   min="0"
                                   step="0.01"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <span id="discountSuffix" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">%</span>
                        </div>
                    </div>

                    <div>
                        <label for="min_purchase" class="block text-sm font-medium text-gray-700 mb-1">
                            Compra Mínima
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                            <input type="number" 
                                   name="min_purchase" 
                                   id="min_purchase" 
                                   value="{{ old('min_purchase') }}"
                                   min="0"
                                   step="100"
                                   class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div>
                        <label for="max_discount" class="block text-sm font-medium text-gray-700 mb-1">
                            Descuento Máximo
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                            <input type="number" 
                                   name="max_discount" 
                                   id="max_discount" 
                                   value="{{ old('max_discount') }}"
                                   min="0"
                                   step="100"
                                   class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Buy X Get Y Fields -->
                <div id="buyXGetYFields" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="buy_quantity" class="block text-sm font-medium text-gray-700 mb-1">
                            Cantidad a Comprar
                        </label>
                        <input type="number" 
                               name="buy_quantity" 
                               id="buy_quantity" 
                               value="{{ old('buy_quantity', 2) }}"
                               min="1"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="get_quantity" class="block text-sm font-medium text-gray-700 mb-1">
                            Cantidad Gratis
                        </label>
                        <input type="number" 
                               name="get_quantity" 
                               id="get_quantity" 
                               value="{{ old('get_quantity', 1) }}"
                               min="1"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedule -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Programación</h3>
            
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha de Inicio
                        </label>
                        <input type="date" 
                               name="starts_at" 
                               id="starts_at" 
                               value="{{ old('starts_at') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="ends_at" class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha de Fin
                        </label>
                        <input type="date" 
                               name="ends_at" 
                               id="ends_at" 
                               value="{{ old('ends_at') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- Time Restriction (Happy Hour) -->
                <div id="timeFields" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">
                                Hora de Inicio
                            </label>
                            <input type="time" 
                                   name="start_time" 
                                   id="start_time" 
                                   value="{{ old('start_time') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">
                                Hora de Fin
                            </label>
                            <input type="time" 
                                   name="end_time" 
                                   id="end_time" 
                                   value="{{ old('end_time') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Days of Week -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Días Aplicables
                    </label>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'] as $index => $day)
                        <label class="inline-flex items-center">
                            <input type="checkbox" 
                                   name="applicable_days[]" 
                                   value="{{ $index }}"
                                   {{ in_array($index, old('applicable_days', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">{{ $day }}</span>
                        </label>
                        @endforeach
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Dejar vacío para aplicar todos los días</p>
                </div>
            </div>
        </div>

        <!-- Usage Limits -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Límites de Uso</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="usage_limit" class="block text-sm font-medium text-gray-700 mb-1">
                        Límite Total de Usos
                    </label>
                    <input type="number" 
                           name="usage_limit" 
                           id="usage_limit" 
                           value="{{ old('usage_limit') }}"
                           min="1"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Sin límite">
                </div>

                <div>
                    <label for="usage_per_customer" class="block text-sm font-medium text-gray-700 mb-1">
                        Límite por Cliente
                    </label>
                    <input type="number" 
                           name="usage_per_customer" 
                           id="usage_per_customer" 
                           value="{{ old('usage_per_customer') }}"
                           min="1"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Sin límite">
                </div>
            </div>
        </div>

        <!-- Product Restrictions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Productos Aplicables</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Categorías
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 max-h-40 overflow-y-auto p-2 border border-gray-200 rounded-lg">
                        @foreach($categories as $category)
                        <label class="inline-flex items-center">
                            <input type="checkbox" 
                                   name="applicable_categories[]" 
                                   value="{{ $category->id }}"
                                   {{ in_array($category->id, old('applicable_categories', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">{{ $category->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Dejar vacío para aplicar a todas las categorías</p>
                </div>
            </div>
        </div>

        <!-- Options -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Opciones</h3>
            
            <div class="space-y-3">
                <label class="flex items-center gap-3">
                    <input type="checkbox" 
                           name="is_active" 
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="text-sm text-gray-700">Activar promoción inmediatamente</span>
                </label>

                <label class="flex items-center gap-3">
                    <input type="checkbox" 
                           name="is_combinable" 
                           value="1"
                           {{ old('is_combinable') ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="text-sm text-gray-700">Permitir combinar con otras promociones</span>
                </label>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.promotions.index') }}" 
               class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-6 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                Crear Promoción
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function toggleTypeFields() {
        const type = document.getElementById('type').value;
        const discountFields = document.getElementById('discountValueFields');
        const buyXGetYFields = document.getElementById('buyXGetYFields');
        const timeFields = document.getElementById('timeFields');
        const discountLabel = document.getElementById('discountLabel');
        const discountSuffix = document.getElementById('discountSuffix');

        // Reset visibility
        discountFields.classList.remove('hidden');
        buyXGetYFields.classList.add('hidden');
        timeFields.classList.add('hidden');

        switch(type) {
            case 'percentage':
            case 'happy_hour':
                discountLabel.textContent = 'Porcentaje';
                discountSuffix.textContent = '%';
                if (type === 'happy_hour') {
                    timeFields.classList.remove('hidden');
                }
                break;
            case 'fixed':
                discountLabel.textContent = 'Monto';
                discountSuffix.textContent = '$';
                break;
            case 'buy_x_get_y':
                discountFields.classList.add('hidden');
                buyXGetYFields.classList.remove('hidden');
                break;
            case 'free_delivery':
                discountFields.classList.add('hidden');
                break;
        }
    }

    // Initial state
    document.addEventListener('DOMContentLoaded', toggleTypeFields);
</script>
@endpush
@endsection
