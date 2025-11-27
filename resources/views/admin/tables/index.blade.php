<x-layouts.app>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="md:flex md:items-center md:justify-between mb-6">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        Gestión de Mesas
                    </h2>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4 gap-2">
                    <a href="{{ route('admin.zones.index') }}" class="btn-secondary">
                        Gestionar Zonas
                    </a>
                    <a href="{{ route('admin.tables.create') }}" class="btn-primary">
                        + Nueva Mesa
                    </a>
                </div>
            </div>

            <!-- Zones and Tables -->
            @foreach($zones as $zone)
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-4 h-4 rounded-full" style="background-color: {{ $zone->color }}"></div>
                            <h3 class="text-lg font-medium text-gray-900">{{ $zone->name }}</h3>
                            <span class="text-sm text-gray-500">({{ $zone->tables->count() }} mesas)</span>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.zones.edit', $zone) }}" class="text-blue-600 hover:text-blue-900 text-sm">
                                Editar Zona
                            </a>
                        </div>
                    </div>
                </div>

                <div class="px-4 py-5 sm:p-6">
                    @if($zone->tables->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($zone->tables as $table)
                        <div class="relative bg-white border-2 rounded-lg p-4 hover:shadow-md transition-shadow
                            {{ $table->status === 'free' ? 'border-green-300' : '' }}
                            {{ $table->status === 'occupied' ? 'border-red-300 bg-red-50' : '' }}
                            {{ $table->status === 'reserved' ? 'border-yellow-300 bg-yellow-50' : '' }}">
                            
                            <!-- Status Badge -->
                            <div class="absolute top-2 right-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    {{ $table->status === 'free' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $table->status === 'occupied' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $table->status === 'reserved' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                    {{ $table->status === 'free' ? 'Libre' : ($table->status === 'occupied' ? 'Ocupada' : 'Reservada') }}
                                </span>
                            </div>

                            <!-- Table Info -->
                            <div class="mt-4 text-center">
                                <div class="text-3xl font-bold text-gray-900">{{ $table->number }}</div>
                                @if($table->name)
                                <div class="text-sm text-gray-600 mt-1">{{ $table->name }}</div>
                                @endif
                                <div class="text-xs text-gray-500 mt-2">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    {{ $table->capacity }} personas
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="mt-4 flex gap-1">
                                <a href="{{ route('admin.tables.edit', $table) }}" 
                                   class="flex-1 text-center px-2 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
                                    Editar
                                </a>
                                @if($table->is_active)
                                <form action="{{ route('admin.tables.destroy', $table) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('¿Está seguro de eliminar esta mesa?')"
                                            class="w-full px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">
                                        Eliminar
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        <p>No hay mesas en esta zona</p>
                        <a href="{{ route('admin.tables.create') }}" class="text-blue-600 hover:text-blue-800 text-sm mt-2 inline-block">
                            Agregar primera mesa
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach

            @if($zones->count() === 0)
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-12 text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay zonas configuradas</h3>
                    <p class="text-gray-500 mb-4">Primero debe crear zonas para poder agregar mesas</p>
                    <a href="{{ route('admin.zones.create') }}" class="btn-primary">
                        Crear Primera Zona
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-layouts.app>
