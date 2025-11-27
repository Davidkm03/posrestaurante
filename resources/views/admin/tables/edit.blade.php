<x-layouts.app title="Editar Mesa {{ $table->number }}">
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.tables.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Editar Mesa {{ $table->number }}</h1>
                <p class="text-sm text-gray-500">Modifique los datos de la mesa</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <form action="{{ route('admin.tables.update', $table) }}" method="POST">
            @csrf
            @method('PUT')

            <x-card>
                <div class="space-y-6">
                    <!-- Zona -->
                    <x-select 
                        name="zone_id" 
                        label="Zona" 
                        required 
                        placeholder="Seleccione una zona"
                        :options="$zones->pluck('name', 'id')->toArray()"
                        :selected="$table->zone_id"
                    />

                    <!-- Información básica -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-input 
                            name="number" 
                            label="Número de Mesa" 
                            required 
                            placeholder="Ej: 1, A1, M-01"
                            :value="$table->number"
                        />
                        
                        <x-input 
                            name="name" 
                            label="Nombre (opcional)" 
                            placeholder="Ej: Terraza 1, VIP"
                            :value="$table->name"
                        />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-input 
                            type="number" 
                            name="capacity" 
                            label="Capacidad (personas)" 
                            required 
                            :value="$table->capacity"
                            min="1" 
                            max="50"
                        />
                        
                        <x-select 
                            name="shape" 
                            label="Forma de la mesa"
                            :options="[
                                'square' => 'Cuadrada',
                                'round' => 'Redonda',
                                'rectangle' => 'Rectangular'
                            ]"
                            :selected="$table->shape"
                        />
                    </div>

                    <!-- Posición en el mapa -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-sm font-medium text-gray-900 mb-4">Posición en el Mapa</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <x-input 
                                type="number" 
                                name="position_x" 
                                label="Posición X" 
                                :value="$table->position_x"
                                hint="Coordenada horizontal"
                            />
                            <x-input 
                                type="number" 
                                name="position_y" 
                                label="Posición Y" 
                                :value="$table->position_y"
                                hint="Coordenada vertical"
                            />
                            <x-input 
                                type="number" 
                                name="width" 
                                label="Ancho" 
                                :value="$table->width"
                            />
                            <x-input 
                                type="number" 
                                name="height" 
                                label="Alto" 
                                :value="$table->height"
                            />
                        </div>
                    </div>

                    <!-- Estado -->
                    <div class="border-t border-gray-200 pt-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-select 
                                name="status" 
                                label="Estado"
                                :options="collect($statuses)->mapWithKeys(fn($s) => [
                                    $s->value => match($s->value) {
                                        'free' => 'Libre',
                                        'occupied' => 'Ocupada',
                                        'reserved' => 'Reservada',
                                        default => $s->value
                                    }
                                ])->toArray()"
                                :selected="$table->status->value ?? $table->status"
                            />

                            <div class="flex items-end pb-2">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" 
                                           {{ $table->is_active ? 'checked' : '' }}
                                           class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">Mesa activa</span>
                                        <p class="text-xs text-gray-500">Disponible para uso</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="flex justify-between">
                        <form action="{{ route('admin.tables.destroy', $table) }}" method="POST" 
                              onsubmit="return confirm('¿Está seguro de eliminar esta mesa?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2.5 text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Eliminar
                            </button>
                        </form>
                        
                        <div class="flex gap-3">
                            <a href="{{ route('admin.tables.index') }}" 
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
