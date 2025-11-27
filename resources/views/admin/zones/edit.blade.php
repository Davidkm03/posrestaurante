<x-layouts.app title="Editar Zona: {{ $zone->name }}">
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.zones.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Editar Zona</h1>
                <p class="text-sm text-gray-500">{{ $zone->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <form action="{{ route('admin.zones.update', $zone) }}" method="POST">
            @csrf
            @method('PUT')

            <x-card>
                <div class="space-y-6">
                    <!-- Nombre -->
                    <x-input 
                        name="name" 
                        label="Nombre de la Zona" 
                        required 
                        :value="$zone->name"
                        placeholder="Ej: Terraza, Salón Principal, Barra"
                    />

                    <!-- Descripción -->
                    <x-textarea 
                        name="description" 
                        label="Descripción (opcional)" 
                        rows="3"
                        :value="$zone->description"
                        placeholder="Descripción breve de la zona..."
                    />

                    <!-- Color y Orden -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                            <div class="flex items-center gap-3">
                                <input type="color" name="color" value="{{ $zone->color ?? '#3B82F6' }}" 
                                       class="w-12 h-12 rounded-lg border-2 border-gray-200 cursor-pointer p-1">
                                <span class="text-sm text-gray-500">Color para identificar la zona</span>
                            </div>
                        </div>
                        
                        <x-input 
                            type="number" 
                            name="sort_order" 
                            label="Orden de visualización" 
                            :value="$zone->sort_order"
                            min="0"
                            hint="Número menor = aparece primero"
                        />
                    </div>

                    <!-- Estado -->
                    <div class="border-t border-gray-200 pt-6">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" 
                                   {{ $zone->is_active ? 'checked' : '' }}
                                   class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <div>
                                <span class="text-sm font-medium text-gray-900">Zona activa</span>
                                <p class="text-xs text-gray-500">Las zonas inactivas no se muestran en el POS</p>
                            </div>
                        </label>
                    </div>

                    <!-- Info de mesas -->
                    @if($zone->tables()->count() > 0)
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm text-blue-800">
                                    Esta zona tiene <strong>{{ $zone->tables()->count() }} mesas</strong> asignadas.
                                    <a href="{{ route('admin.tables.index') }}" class="underline">Ver mesas</a>
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                <x-slot name="footer">
                    <div class="flex justify-between">
                        @if($zone->tables()->count() == 0)
                            <form action="{{ route('admin.zones.destroy', $zone) }}" method="POST" 
                                  onsubmit="return confirm('¿Está seguro de eliminar esta zona?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2.5 text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                                    <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Eliminar
                                </button>
                            </form>
                        @else
                            <div></div>
                        @endif
                        
                        <div class="flex gap-3">
                            <a href="{{ route('admin.zones.index') }}" 
                               class="px-4 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                Cancelar
                            </a>
                            <x-button type="submit">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Guardar Cambios
                            </x-button>
                        </div>
                    </div>
                </x-slot>
            </x-card>
        </form>
    </div>
</x-layouts.app>
