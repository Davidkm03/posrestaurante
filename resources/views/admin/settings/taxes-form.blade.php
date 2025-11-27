<x-layouts.app title="{{ $tax ? 'Editar' : 'Nuevo' }} Impuesto">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $tax ? 'Editar Impuesto' : 'Nuevo Impuesto' }}</h1>
                <p class="text-sm text-gray-500">{{ $tax ? 'Actualiza la información del impuesto' : 'Configura un nuevo impuesto' }}</p>
            </div>
            <a href="{{ route('admin.settings.taxes') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl">
        <x-card>
            <form action="{{ $tax ? route('admin.settings.taxes.update', $tax) : route('admin.settings.taxes.store') }}" method="POST">
                @csrf
                @if($tax)
                    @method('PUT')
                @endif

                <div class="space-y-6">
                    <!-- Nombre -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Nombre del Impuesto <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            value="{{ old('name', $tax->name ?? '') }}"
                            placeholder="Ej: IVA, Impoconsumo"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                            required
                        >
                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Código -->
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
                                Código <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="code" 
                                id="code" 
                                value="{{ old('code', $tax->code ?? '') }}"
                                placeholder="Ej: IVA19"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('code') border-red-500 @enderror"
                                required
                            >
                            @error('code')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Código DIAN -->
                        <div>
                            <label for="dian_code" class="block text-sm font-medium text-gray-700 mb-1">
                                Código DIAN <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="dian_code" 
                                id="dian_code" 
                                value="{{ old('dian_code', $tax->dian_code ?? '') }}"
                                placeholder="Ej: 01, 04"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('dian_code') border-red-500 @enderror"
                                required
                            >
                            @error('dian_code')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">01 = IVA, 04 = Impoconsumo</p>
                        </div>
                    </div>

                    <!-- Porcentaje -->
                    <div>
                        <label for="percentage" class="block text-sm font-medium text-gray-700 mb-1">
                            Porcentaje (%) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="number" 
                                name="percentage" 
                                id="percentage" 
                                value="{{ old('percentage', $tax->percentage ?? '') }}"
                                step="0.01" 
                                min="0" 
                                max="100"
                                placeholder="19"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10 @error('percentage') border-red-500 @enderror"
                                required
                            >
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">%</span>
                        </div>
                        @error('percentage')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Switch Activo -->
                    <div class="space-y-4 pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="is_active" class="text-sm font-medium text-gray-700">Activo</label>
                                <p class="text-xs text-gray-500">El impuesto estará disponible para usar</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="is_active" value="0">
                                <input 
                                    type="checkbox" 
                                    name="is_active" 
                                    id="is_active" 
                                    value="1"
                                    {{ old('is_active', $tax->is_active ?? true) ? 'checked' : '' }}
                                    class="sr-only peer"
                                >
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Presets Colombia (solo para nuevo) -->
                    @unless($tax)
                    <div class="pt-4 border-t border-gray-200">
                        <p class="text-sm font-medium text-gray-700 mb-3">Presets Impuestos Colombia</p>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" onclick="applyPreset('IVA 19%', 'IVA19', '01', 19)" 
                                    class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm transition-colors">
                                IVA 19%
                            </button>
                            <button type="button" onclick="applyPreset('IVA 5%', 'IVA5', '01', 5)" 
                                    class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm transition-colors">
                                IVA 5%
                            </button>
                            <button type="button" onclick="applyPreset('Exento', 'EXE', '01', 0)" 
                                    class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm transition-colors">
                                Exento
                            </button>
                            <button type="button" onclick="applyPreset('Impoconsumo 8%', 'INC8', '04', 8)" 
                                    class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm transition-colors">
                                Impoconsumo 8%
                            </button>
                        </div>
                    </div>
                    @endunless
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-between gap-4 pt-6 mt-6 border-t border-gray-200">
                    @if($tax)
                    <button 
                        type="button"
                        onclick="if(confirm('¿Estás seguro de eliminar este impuesto?')) { document.getElementById('delete-form').submit(); }"
                        class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg"
                    >
                        Eliminar
                    </button>
                    @else
                    <div></div>
                    @endif
                    <div class="flex gap-3">
                        <a href="{{ route('admin.settings.taxes') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            Cancelar
                        </a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            {{ $tax ? 'Guardar Cambios' : 'Crear Impuesto' }}
                        </button>
                    </div>
                </div>
            </form>

            @if($tax)
            <!-- Formulario de eliminación -->
            <form id="delete-form" action="{{ route('admin.settings.taxes.destroy', $tax) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
            @endif
        </x-card>
    </div>

    @push('scripts')
    <script>
        function applyPreset(name, code, dianCode, percentage) {
            document.getElementById('name').value = name;
            document.getElementById('code').value = code;
            document.getElementById('dian_code').value = dianCode;
            document.getElementById('percentage').value = percentage;
        }
    </script>
    @endpush
</x-layouts.app>
