<x-layouts.app title="Zonas">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Zonas del Restaurante</h1>
                <p class="text-sm text-gray-500">Organiza las mesas por zonas (terraza, salón, barra, etc.)</p>
            </div>
            <a href="{{ route('admin.zones.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nueva Zona
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>
    @endif

    @if(session('error'))
        <x-alert type="danger" class="mb-6">{{ session('error') }}</x-alert>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($zones as $zone)
            <x-card :padding="false" class="overflow-hidden hover:shadow-lg transition-shadow">
                <!-- Color Bar -->
                <div class="h-3" style="background-color: {{ $zone->color ?? '#6B7280' }}"></div>
                
                <div class="p-5">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center" 
                                 style="background-color: {{ $zone->color ?? '#6B7280' }}20">
                                <svg class="w-6 h-6" style="color: {{ $zone->color ?? '#6B7280' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 text-lg">{{ $zone->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $zone->tables_count }} mesas</p>
                            </div>
                        </div>
                        
                        @if($zone->is_active)
                            <x-badge type="success" size="sm">Activa</x-badge>
                        @else
                            <x-badge type="danger" size="sm">Inactiva</x-badge>
                        @endif
                    </div>

                    <!-- Description -->
                    @if($zone->description)
                        <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $zone->description }}</p>
                    @endif

                    <!-- Stats -->
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ $zone->tables_count }}</p>
                            <p class="text-xs text-gray-500">Total Mesas</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-3 text-center">
                            <p class="text-2xl font-bold text-green-600">{{ $zone->getAvailableTablesCount() }}</p>
                            <p class="text-xs text-gray-500">Disponibles</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <span class="text-xs text-gray-400">Orden: {{ $zone->sort_order }}</span>
                        
                        <div class="flex items-center gap-1">
                            <a href="{{ route('admin.zones.edit', $zone) }}" 
                               class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                               title="Editar">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            
                            @if($zone->tables_count == 0)
                                <form action="{{ route('admin.zones.destroy', $zone) }}" method="POST" 
                                      onsubmit="return confirm('¿Eliminar esta zona?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Eliminar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </x-card>
        @empty
            <div class="col-span-full">
                <x-empty-state 
                    title="No hay zonas configuradas" 
                    description="Crea zonas para organizar las mesas de tu restaurante (terraza, salón principal, barra, etc.)">
                    <x-slot name="icon">
                        <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </x-slot>
                    <x-slot name="action">
                        <a href="{{ route('admin.zones.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Crear Primera Zona
                        </a>
                    </x-slot>
                </x-empty-state>
            </div>
        @endforelse
    </div>

    <!-- Link to Tables -->
    @if($zones->count() > 0)
        <div class="mt-8 text-center">
            <a href="{{ route('admin.tables.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                Ver todas las mesas →
            </a>
        </div>
    @endif
</x-layouts.app>
