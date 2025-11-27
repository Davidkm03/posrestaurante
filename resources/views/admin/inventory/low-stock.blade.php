<x-layouts.app title="Productos con Stock Bajo">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Productos con Stock Bajo</h1>
                <p class="text-sm text-gray-500">Productos que necesitan reabastecimiento</p>
            </div>
            <a href="{{ route('admin.inventory.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    @if($products->isEmpty())
        <x-card>
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">¡Todo en orden!</h3>
                <p class="mt-2 text-gray-500">No hay productos con stock bajo en este momento.</p>
            </div>
        </x-card>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($products as $product)
                @php
                    $percentage = $product->min_stock > 0 ? ($product->stock / $product->min_stock) * 100 : 0;
                    $isOutOfStock = $product->stock <= 0;
                @endphp
                <x-card class="{{ $isOutOfStock ? 'ring-2 ring-red-500' : 'ring-1 ring-yellow-400' }}">
                    <div class="flex items-start gap-4">
                        @if($product->image)
                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-16 h-16 rounded-lg object-cover">
                        @else
                            <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 truncate">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $product->category->name ?? 'Sin categoría' }}</p>
                            
                            <div class="mt-3">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="{{ $isOutOfStock ? 'text-red-600' : 'text-yellow-600' }} font-medium">
                                        Stock: {{ number_format($product->stock) }}
                                    </span>
                                    <span class="text-gray-500">
                                        Mín: {{ number_format($product->min_stock) }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $isOutOfStock ? 'bg-red-500' : 'bg-yellow-500' }}" 
                                        style="width: {{ min($percentage, 100) }}%"></div>
                                </div>
                            </div>

                            <div class="mt-3 flex gap-2">
                                <a href="{{ route('admin.inventory.adjust', $product) }}" 
                                    class="flex-1 text-center px-3 py-1.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    Ajustar
                                </a>
                                <a href="{{ route('admin.inventory.show', $product) }}" 
                                    class="px-3 py-1.5 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                    Ver
                                </a>
                            </div>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif
</x-layouts.app>
