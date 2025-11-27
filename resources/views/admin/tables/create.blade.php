<x-layouts.app title="Nueva Mesa">
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.tables.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Nueva Mesa</h1>
                <p class="text-sm text-gray-500">Complete los datos para crear una nueva mesa</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <form action="{{ route('admin.tables.store') }}" method="POST">
            @csrf

            <x-card>
                <div class="space-y-6">
                    <!-- Zona -->
                    <x-select 
                        name="zone_id" 
                        label="Zona" 
                        required 
                        placeholder="Seleccione una zona"
                        :options="$zones->pluck('name', 'id')->toArray()"
                    />
                    
                    @if($zones->isEmpty())
                        <x-alert type="warning">
                            No hay zonas disponibles. 
                            <a href="{{ route('admin.zones.create') }}" class="underline font-medium">Cree una zona primero</a>.
                        </x-alert>
                    @endif

                    <!-- Información básica -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-input 
                            name="number" 
                            label="Número de Mesa" 
                            required 
                            placeholder="Ej: 1, A1, M-01"
                        />
                        
                        <x-input 
                            name="name" 
                            label="Nombre (opcional)" 
                            placeholder="Ej: Terraza 1, VIP"
                        />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-input 
                            type="number" 
                            name="capacity" 
                            label="Capacidad (personas)" 
                            required 
                            value="4"
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
                                value="0"
                                hint="Coordenada horizontal"
                            />
                            <x-input 
                                type="number" 
                                name="position_y" 
                                label="Posición Y" 
                                value="0"
                                hint="Coordenada vertical"
                            />
                            <x-input 
                                type="number" 
                                name="width" 
                                label="Ancho" 
                                value="100"
                            />
                            <x-input 
                                type="number" 
                                name="height" 
                                label="Alto" 
                                value="100"
                            />
                        </div>
                    </div>

                    <!-- Estado -->
                    <div class="border-t border-gray-200 pt-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-select 
                                name="status" 
                                label="Estado inicial"
                                :options="collect($statuses)->mapWithKeys(fn($s) => [
                                    $s->value => match($s->value) {
                                        'free' => 'Libre',
                                        'occupied' => 'Ocupada',
                                        'reserved' => 'Reservada',
                                        default => $s->value
                                    }
                                ])->toArray()"
                                selected="free"
                            />

                            <div class="flex items-end pb-2">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" checked
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
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('admin.tables.index') }}" 
                           class="px-4 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancelar
                        </a>
                        <x-button type="submit">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Crear Mesa
                        </x-button>
                    </div>
                </x-slot>
            </x-card>
        </form>
    </div>
</x-layouts.app>
