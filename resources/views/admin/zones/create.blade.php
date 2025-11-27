<x-layouts.app title="Nueva Zona">
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.zones.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Nueva Zona</h1>
                <p class="text-sm text-gray-500">Crea una nueva zona para organizar las mesas</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <form action="{{ route('admin.zones.store') }}" method="POST">
            @csrf

            <x-card>
                <div class="space-y-6">
                    <!-- Nombre -->
                    <x-input 
                        name="name" 
                        label="Nombre de la Zona" 
                        required 
                        placeholder="Ej: Terraza, Salón Principal, Barra"
                    />

                    <!-- Descripción -->
                    <x-textarea 
                        name="description" 
                        label="Descripción (opcional)" 
                        rows="3"
                        placeholder="Descripción breve de la zona..."
                    />

                    <!-- Color y Orden -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                            <div class="flex items-center gap-3">
                                <input type="color" name="color" value="#3B82F6" 
                                       class="w-12 h-12 rounded-lg border-2 border-gray-200 cursor-pointer p-1">
                                <span class="text-sm text-gray-500">Selecciona un color para identificar la zona</span>
                            </div>
                        </div>
                        
                        <x-input 
                            type="number" 
                            name="sort_order" 
                            label="Orden de visualización" 
                            value="0"
                            min="0"
                            hint="Número menor = aparece primero"
                        />
                    </div>

                    <!-- Estado -->
                    <div class="border-t border-gray-200 pt-6">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked
                                   class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <div>
                                <span class="text-sm font-medium text-gray-900">Zona activa</span>
                                <p class="text-xs text-gray-500">Las zonas inactivas no se muestran en el POS</p>
                            </div>
                        </label>
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('admin.zones.index') }}" 
                           class="px-4 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancelar
                        </a>
                        <x-button type="submit">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Crear Zona
                        </x-button>
                    </div>
                </x-slot>
            </x-card>
        </form>
    </div>
</x-layouts.app>
