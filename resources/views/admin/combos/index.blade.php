<x-layouts.app title="Admin">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Combos</h1>
                <p class="text-sm text-gray-500">Gestiona combos y paquetes de productos</p>
            </div>
            <a href="{{ route('admin.combos.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nuevo Combo
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($combos ?? [] as $combo)
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="aspect-video bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center">
                            @if($combo->image)
                                <img src="{{ Storage::url($combo->image) }}" alt="{{ $combo->name }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="p-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $combo->name }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ $combo->products_count ?? 0 }} productos</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full {{ $combo->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $combo->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                            
                            <div class="mt-3 flex items-center justify-between">
                                <div>
                                    @if($combo->discount_type === 'percentage')
                                        <span class="text-lg font-bold text-green-600">{{ $combo->discount_value }}% OFF</span>
                                    @else
                                        <span class="text-lg font-bold text-gray-900">${{ number_format($combo->price, 0) }}</span>
                                        @if($combo->original_price > $combo->price)
                                            <span class="text-sm text-gray-400 line-through ml-2">${{ number_format($combo->original_price, 0) }}</span>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4 flex gap-2">
                                <a href="{{ route('admin.combos.edit', $combo) }}" class="flex-1 text-center px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">
                                    Editar
                                </a>
                                <form action="{{ route('admin.combos.destroy', $combo) }}" method="POST" class="flex-1" onsubmit="return confirm('¿Eliminar este combo?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 text-sm">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay combos</h3>
                            <p class="mt-1 text-sm text-gray-500">Crea combos para ofrecer descuentos en paquetes de productos.</p>
                            <div class="mt-6">
                                <a href="{{ route('admin.combos.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Crear Combo
                                </a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>
