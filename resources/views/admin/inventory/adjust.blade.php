<x-layouts.app title="Ajustar Stock - {{ $product->name }}">
    <x-slot name="header">
        <div>
            <nav class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                <a href="{{ route('admin.inventory.index') }}" class="hover:text-blue-600">Inventario</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <a href="{{ route('admin.inventory.show', $product) }}" class="hover:text-blue-600">{{ $product->name }}</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span>Ajustar Stock</span>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900">Ajustar Stock</h1>
            <p class="text-sm text-gray-500">{{ $product->name }}</p>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <!-- Info actual -->
        <x-card class="mb-6">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="text-sm text-gray-500">Stock Actual</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($product->stock, $product->unit === 'unidad' ? 0 : 2) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Stock Mínimo</p>
                    <p class="text-xl font-semibold text-gray-700">{{ number_format($product->min_stock) }}</p>
                </div>
            </div>
        </x-card>

        <!-- Formulario de ajuste -->
        <x-card title="Realizar Ajuste">
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.inventory.adjust.store', $product) }}" class="space-y-6">
                @csrf

                <!-- Tipo de ajuste -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Tipo de Ajuste</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="type" value="set" class="sr-only peer" {{ old('type', 'set') === 'set' ? 'checked' : '' }}>
                            <div class="p-4 border-2 rounded-lg text-center transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-gray-300">
                                <svg class="w-6 h-6 mx-auto mb-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-sm font-medium">Establecer</span>
                                <span class="block text-xs text-gray-500">Nuevo total</span>
                            </div>
                        </label>

                        <label class="relative cursor-pointer">
                            <input type="radio" name="type" value="add" class="sr-only peer" {{ old('type') === 'add' ? 'checked' : '' }}>
                            <div class="p-4 border-2 rounded-lg text-center transition-all peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-gray-300">
                                <svg class="w-6 h-6 mx-auto mb-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span class="text-sm font-medium">Agregar</span>
                                <span class="block text-xs text-gray-500">Entrada</span>
                            </div>
                        </label>

                        <label class="relative cursor-pointer">
                            <input type="radio" name="type" value="subtract" class="sr-only peer" {{ old('type') === 'subtract' ? 'checked' : '' }}>
                            <div class="p-4 border-2 rounded-lg text-center transition-all peer-checked:border-red-500 peer-checked:bg-red-50 hover:border-gray-300">
                                <svg class="w-6 h-6 mx-auto mb-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                                <span class="text-sm font-medium">Restar</span>
                                <span class="block text-xs text-gray-500">Salida</span>
                            </div>
                        </label>
                    </div>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Cantidad -->
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" 
                        step="{{ $product->unit === 'unidad' ? '1' : '0.01' }}" min="0" required
                        class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0 focus:outline-none transition-colors text-lg"
                        placeholder="0">
                    @error('quantity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Motivo/Notas -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Motivo del Ajuste *</label>
                    <textarea name="notes" id="notes" rows="3" required
                        class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0 focus:outline-none transition-colors resize-none"
                        placeholder="Describe el motivo del ajuste (ej: Conteo físico, Corrección de error, etc.)">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Acciones -->
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="{{ route('admin.inventory.show', $product) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Guardar Ajuste
                    </button>
                </div>
            </form>
        </x-card>

        <!-- Registrar merma -->
        <x-card title="Registrar Merma/Desperdicio" class="mt-6">
            <form method="POST" action="{{ route('admin.inventory.waste', $product) }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="waste_quantity" class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                        <input type="number" name="quantity" id="waste_quantity" 
                            step="{{ $product->unit === 'unidad' ? '1' : '0.01' }}" min="0.01" required
                            class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-red-500 focus:ring-0 focus:outline-none transition-colors"
                            placeholder="0">
                    </div>
                    <div>
                        <label for="waste_notes" class="block text-sm font-medium text-gray-700 mb-1">Motivo</label>
                        <input type="text" name="notes" id="waste_notes" required
                            class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-red-500 focus:ring-0 focus:outline-none transition-colors"
                            placeholder="Ej: Producto vencido, Daño, etc.">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Registrar Merma
                    </button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>
