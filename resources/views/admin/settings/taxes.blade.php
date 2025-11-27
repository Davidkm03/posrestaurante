<x-layouts.app title="Impuestos">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Impuestos</h1>
                <p class="text-sm text-gray-500">Configura los impuestos para tus productos y facturas</p>
            </div>
            <a href="{{ route('admin.settings.taxes.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nuevo Impuesto
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($taxes ?? [] as $tax)
            <x-card :padding="false" class="overflow-hidden">
                <div class="h-2 {{ $tax->is_active ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                <div class="p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-blue-50">
                                <span class="text-lg font-bold text-blue-600">{{ number_format($tax->percentage, 0) }}%</span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $tax->name }}</h3>
                                <p class="text-sm text-gray-500">Código: {{ $tax->code }}</p>
                            </div>
                        </div>
                        @if($tax->is_active)
                            <x-badge type="success" size="sm">Activo</x-badge>
                        @else
                            <x-badge type="danger" size="sm">Inactivo</x-badge>
                        @endif
                    </div>

                    <div class="mt-3 text-sm text-gray-500">
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            DIAN: {{ $tax->dian_code }}
                        </span>
                    </div>

                    <div class="mt-4 flex items-center justify-end pt-4 border-t border-gray-100">
                        <div class="flex items-center gap-1">
                            <a href="{{ route('admin.settings.taxes.edit', $tax) }}" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <form action="{{ route('admin.settings.taxes.destroy', $tax) }}" method="POST" onsubmit="return confirm('¿Eliminar este impuesto?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </x-card>
        @empty
            <div class="col-span-full">
                <x-empty-state title="No hay impuestos" description="Configura los impuestos que aplican a tus productos según la normativa DIAN">
                    <x-slot name="action">
                        <a href="{{ route('admin.settings.taxes.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Crear Impuesto
                        </a>
                    </x-slot>
                </x-empty-state>
            </div>
        @endforelse
    </div>

    <!-- Sección de Presets Colombia -->
    <div class="mt-8">
        <x-card>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Impuestos Colombia (DIAN)</h3>
                    <p class="text-sm text-gray-500">Configuraciones predefinidas según normativa colombiana</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <a href="{{ route('admin.settings.taxes.create') }}?preset=iva19" 
                   class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition-colors group">
                    <div>
                        <span class="font-medium text-gray-900 group-hover:text-blue-700">IVA General</span>
                        <p class="text-xs text-gray-500">Código DIAN: 01</p>
                    </div>
                    <span class="text-xl font-bold text-blue-600">19%</span>
                </a>

                <a href="{{ route('admin.settings.taxes.create') }}?preset=iva5" 
                   class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition-colors group">
                    <div>
                        <span class="font-medium text-gray-900 group-hover:text-blue-700">IVA Reducido</span>
                        <p class="text-xs text-gray-500">Canasta básica</p>
                    </div>
                    <span class="text-xl font-bold text-blue-600">5%</span>
                </a>

                <a href="{{ route('admin.settings.taxes.create') }}?preset=exento" 
                   class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-colors group">
                    <div>
                        <span class="font-medium text-gray-900 group-hover:text-gray-700">Exento de IVA</span>
                        <p class="text-xs text-gray-500">Productos exentos</p>
                    </div>
                    <span class="text-xl font-bold text-gray-500">0%</span>
                </a>

                <a href="{{ route('admin.settings.taxes.create') }}?preset=impoconsumo" 
                   class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-orange-50 hover:border-orange-300 transition-colors group">
                    <div>
                        <span class="font-medium text-gray-900 group-hover:text-orange-700">Impoconsumo</span>
                        <p class="text-xs text-gray-500">Código DIAN: 04</p>
                    </div>
                    <span class="text-xl font-bold text-orange-600">8%</span>
                </a>
            </div>
        </x-card>
    </div>
</x-layouts.app>
