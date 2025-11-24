<div class="flex flex-col h-full">
    <!-- Search Bar -->
    <div class="flex-shrink-0 p-4 bg-gray-800 border-b border-gray-700">
        <div class="relative">
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   wire:keydown.enter="scanBarcode($event.target.value)"
                   placeholder="Buscar producto o escanear código..."
                   class="w-full pl-10 pr-4 py-2 bg-gray-700 text-white rounded-lg border border-gray-600 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
    </div>

    <!-- Categories -->
    <div class="flex-shrink-0 bg-gray-800 border-b border-gray-700">
        <div class="flex overflow-x-auto p-2 gap-2 scrollbar-hide">
            <button wire:click="selectCategory(null)"
                    class="flex-shrink-0 px-4 py-2 rounded-lg font-medium transition-colors {{ is_null($categoryId) ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }}">
                Todos
            </button>
            @foreach($categories as $category)
                <button wire:click="selectCategory({{ $category['id'] }})"
                        class="flex-shrink-0 px-4 py-2 rounded-lg font-medium transition-colors {{ $categoryId === $category['id'] ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }}">
                    {{ $category['name'] }}
                    <span class="ml-1 text-xs opacity-75">({{ $category['products_count'] }})</span>
                </button>
            @endforeach
        </div>
    </div>

    <!-- Products Grid -->
    <div class="flex-1 overflow-y-auto p-4">
        @if($products->isEmpty())
            <div class="flex flex-col items-center justify-center h-full text-gray-500">
                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <p class="text-lg">No se encontraron productos</p>
                @if($search)
                    <p class="text-sm mt-1">Intenta con otro término de búsqueda</p>
                @endif
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
                @foreach($products as $product)
                    <button wire:click="selectProduct({{ $product->id }})"
                            wire:key="product-{{ $product->id }}"
                            class="bg-gray-700 hover:bg-gray-600 rounded-lg p-3 text-left transition-all transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @if($product->image)
                            <div class="aspect-square mb-2 rounded-lg overflow-hidden bg-gray-600">
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="aspect-square mb-2 rounded-lg bg-gray-600 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        <p class="text-white font-medium text-sm truncate">{{ $product->name }}</p>
                        <p class="text-green-400 font-bold">${{ number_format($product->price, 0, ',', '.') }}</p>
                        @if($product->modifierGroups->isNotEmpty())
                            <span class="inline-block mt-1 px-2 py-0.5 bg-blue-600/30 text-blue-400 text-xs rounded">
                                + Opciones
                            </span>
                        @endif
                    </button>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            @endif
        @endif
    </div>

    <!-- Modifier Modal -->
    @if($showModifierModal && $selectedProduct)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70" wire:click.self="closeModifierModal">
            <div class="bg-gray-800 rounded-xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-hidden">
                <!-- Modal Header -->
                <div class="p-4 border-b border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-white">{{ $selectedProduct->name }}</h3>
                            <p class="text-green-400 font-bold">${{ number_format($selectedProduct->price, 0, ',', '.') }}</p>
                        </div>
                        <button wire:click="closeModifierModal" class="p-2 text-gray-400 hover:text-white rounded-lg hover:bg-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="p-4 overflow-y-auto max-h-[60vh] space-y-4">
                    @foreach($selectedProduct->modifierGroups as $group)
                        <div class="bg-gray-700/50 rounded-lg p-3">
                            <h4 class="font-medium text-white mb-2">
                                {{ $group->name }}
                                @if($group->is_required)
                                    <span class="text-red-400 text-sm">*</span>
                                @endif
                                @if($group->max_selections > 1)
                                    <span class="text-gray-400 text-sm">(Máx: {{ $group->max_selections }})</span>
                                @endif
                            </h4>
                            <div class="space-y-2">
                                @foreach($group->modifiers as $modifier)
                                    <label class="flex items-center justify-between p-2 bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-600 transition-colors">
                                        <div class="flex items-center gap-3">
                                            @if($group->max_selections === 1)
                                                <input type="radio"
                                                       name="group-{{ $group->id }}"
                                                       wire:click="toggleModifier({{ $modifier->id }}, {{ $group->id }}, false)"
                                                       @checked(in_array($modifier->id, $selectedModifiers))
                                                       class="w-4 h-4 text-blue-600 bg-gray-600 border-gray-500">
                                            @else
                                                <input type="checkbox"
                                                       wire:click="toggleModifier({{ $modifier->id }}, {{ $group->id }}, true)"
                                                       @checked(in_array($modifier->id, $selectedModifiers))
                                                       class="w-4 h-4 text-blue-600 bg-gray-600 border-gray-500 rounded">
                                            @endif
                                            <span class="text-white">{{ $modifier->name }}</span>
                                        </div>
                                        @if($modifier->price > 0)
                                            <span class="text-green-400">+${{ number_format($modifier->price, 0, ',', '.') }}</span>
                                        @endif
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Notas especiales</label>
                        <textarea wire:model="productNotes"
                                  rows="2"
                                  placeholder="Ej: Sin cebolla, término medio..."
                                  class="w-full bg-gray-700 text-white rounded-lg border border-gray-600 p-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t border-gray-700">
                    <button wire:click="confirmProductWithModifiers"
                            class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                        Agregar al Carrito
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
