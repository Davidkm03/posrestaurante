<div class="h-full flex flex-col bg-gray-50" x-data="{ showCart: false }">
    <!-- Table Info Header -->
    <div class="bg-white border-b border-gray-200 px-4 py-3 flex-shrink-0">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-gray-900">Mesa {{ $table->number }}</h2>
                    <span class="text-sm text-gray-500">{{ $table->zone->name }}</span>
                </div>
                @if($order)
                    <p class="text-sm text-blue-600 font-medium mt-1">
                        Orden activa: {{ $order->order_number }}
                    </p>
                @else
                    <p class="text-sm text-green-600 font-medium mt-1">
                        Nueva orden
                    </p>
                @endif
            </div>
                
            <!-- Guests Counter -->
            <div class="flex items-center gap-2">
                <button wire:click="$set('guests', {{ max(1, $guests - 1) }})" 
                        class="w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 active:scale-95 transition-all flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                </button>
                <div class="text-center min-w-[60px]">
                    <div class="text-2xl font-bold">{{ $guests }}</div>
                    <div class="text-xs text-gray-500 hidden sm:block">Comensales</div>
                </div>
                <button wire:click="$set('guests', {{ $guests + 1 }})" 
                        class="w-10 h-10 rounded-lg bg-blue-500 hover:bg-blue-600 text-white active:scale-95 transition-all flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 overflow-hidden flex flex-col md:flex-row">
        <!-- Products Section -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Search Bar -->
            <div class="p-3 bg-white border-b border-gray-200 flex-shrink-0">
                <input 
                    type="search" 
                    wire:model.live.debounce.300ms="searchTerm"
                    placeholder="Buscar productos..." 
                    class="w-full px-4 py-2.5 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-0 focus:outline-none transition-colors text-base"
                >
            </div>

            <!-- Categories -->
            @if(!$searchTerm)
                <div class="p-3 bg-white border-b border-gray-200 flex-shrink-0">
                    <div class="flex gap-2 overflow-x-auto pb-2 -mb-2 scrollbar-hide">
                        @foreach($categories as $category)
                            <button 
                                wire:click="selectCategory({{ $category->id }})"
                                class="flex-shrink-0 px-4 py-2 rounded-lg font-medium text-sm transition-all active:scale-95 {{ $selectedCategory == $category->id ? 'bg-blue-500 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                            >
                                {{ $category->name }}
                                <span class="ml-1 text-xs opacity-75">({{ $category->products_count }})</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Products Grid -->
            <div class="flex-1 overflow-y-auto p-3 pb-24 md:pb-3">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                    @forelse($products as $product)
                        <button 
                            wire:click="addToCart({{ $product->id }})"
                            class="bg-white border-2 border-gray-200 rounded-xl p-3 hover:border-blue-500 hover:shadow-lg transition-all active:scale-95 flex flex-col touch-manipulation"
                        >
                            @if($product->image)
                                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full aspect-square object-cover rounded-lg mb-2">
                            @else
                                <div class="w-full aspect-square bg-gray-100 rounded-lg mb-2 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            <h3 class="font-semibold text-sm mb-1 line-clamp-2 text-left">{{ $product->name }}</h3>
                            <p class="text-lg font-bold text-blue-600">${{ number_format($product->price, 0, ',', '.') }}</p>
                        </button>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No hay productos disponibles</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Cart Sidebar - Desktop -->
        <div class="hidden md:flex md:w-96 bg-white border-l border-gray-200 flex-col">
            <!-- Cart Header -->
            <div class="p-4 border-b border-gray-200 flex-shrink-0">
                <h3 class="text-lg font-bold text-gray-900">Orden ({{ $cartCount }})</h3>
            </div>

            <!-- Cart Items -->
            <div class="flex-1 overflow-y-auto p-4 space-y-3">
                @forelse($cart as $index => $item)
                    <div class="bg-gray-50 rounded-lg p-3 {{ isset($item['existing_item_id']) ? 'border-2 border-blue-200' : '' }}">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-sm truncate">{{ $item['name'] }}</h4>
                                @if(isset($item['existing_item_id']))
                                    <span class="inline-block mt-1 px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded">Ya pedido</span>
                                @endif
                            </div>
                            @if(!isset($item['existing_item_id']))
                                <button 
                                    wire:click="removeItem({{ $index }})"
                                    class="ml-2 p-1 text-red-500 hover:bg-red-50 rounded"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                @if(!isset($item['existing_item_id']))
                                    <button 
                                        wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] - 1 }})"
                                        class="w-8 h-8 rounded-md bg-gray-200 hover:bg-gray-300 active:scale-95 flex items-center justify-center"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                        </svg>
                                    </button>
                                    <span class="w-8 text-center font-bold">{{ $item['quantity'] }}</span>
                                    <button 
                                        wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] + 1 }})"
                                        class="w-8 h-8 rounded-md bg-blue-500 hover:bg-blue-600 text-white active:scale-95 flex items-center justify-center"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                @else
                                    <span class="text-sm text-gray-600">{{ $item['quantity'] }}x</span>
                                @endif
                            </div>
                            <span class="font-bold">${{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                        </div>

                        @if(!isset($item['existing_item_id']))
                            <input 
                                type="text" 
                                wire:model.blur="cart.{{ $index }}.notes"
                                placeholder="Notas especiales..."
                                class="mt-2 w-full px-2 py-1 text-sm border-2 border-gray-200 rounded focus:border-blue-500 focus:ring-0 focus:outline-none transition-colors"
                            >
                        @elseif($item['notes'])
                            <p class="mt-2 text-xs text-gray-600 italic">{{ $item['notes'] }}</p>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Selecciona productos</p>
                    </div>
                @endforelse
            </div>

            <!-- Cart Footer -->
            <div class="border-t border-gray-200 p-4 space-y-3 flex-shrink-0">
                <!-- Notes -->
                <textarea 
                    wire:model="notes"
                    placeholder="Notas generales de la orden..."
                    rows="2"
                    class="w-full px-3 py-2 text-sm border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0 focus:outline-none transition-colors"
                ></textarea>

                <!-- Totals -->
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">${{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>IVA incluido</span>
                        <span>${{ number_format($taxAmount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-200">
                        <span>Total</span>
                        <span class="text-blue-600">${{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Send Button -->
                <button 
                    wire:click="sendToKitchen"
                    @if(empty($cart)) disabled @endif
                    class="w-full py-3.5 bg-green-600 hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold rounded-lg active:scale-95 transition-all flex items-center justify-center gap-2 text-lg touch-manipulation shadow-sm"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    @if($order)
                        Agregar a Orden
                    @else
                        Enviar a Cocina
                    @endif
                </button>
            </div>
        </div>

        <!-- Mobile Cart Button -->
        <button 
            @click="showCart = true"
            class="md:hidden fixed bottom-4 right-4 bg-blue-600 text-white rounded-full w-16 h-16 flex items-center justify-center shadow-lg hover:bg-blue-700 active:scale-95 transition-all z-40"
        >
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            @if($cartCount > 0)
                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-7 h-7 flex items-center justify-center">{{ $cartCount }}</span>
            @endif
        </button>

        <!-- Mobile Cart Drawer -->
        <div 
            x-show="showCart"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="showCart = false"
            class="md:hidden fixed inset-0 bg-black/50 z-50"
            style="display: none;"
        ></div>

        <div 
            x-show="showCart"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="md:hidden fixed right-0 top-0 bottom-0 w-full max-w-sm bg-white shadow-2xl z-50 flex flex-col"
            style="display: none;"
        >
            <!-- Mobile Cart Header -->
            <div class="p-4 border-b border-gray-200 flex items-center justify-between flex-shrink-0">
                <h3 class="text-lg font-bold text-gray-900">Orden ({{ $cartCount }})</h3>
                <button @click="showCart = false" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Mobile Cart Items -->
            <div class="flex-1 overflow-y-auto p-4 space-y-3">
                @forelse($cart as $index => $item)
                    <div class="bg-gray-50 rounded-lg p-3 {{ isset($item['existing_item_id']) ? 'border-2 border-blue-200' : '' }}">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-sm truncate">{{ $item['name'] }}</h4>
                                @if(isset($item['existing_item_id']))
                                    <span class="inline-block mt-1 px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded">Ya pedido</span>
                                @endif
                            </div>
                            @if(!isset($item['existing_item_id']))
                                <button 
                                    wire:click="removeItem({{ $index }})"
                                    class="ml-2 p-1 text-red-500 hover:bg-red-50 rounded"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                @if(!isset($item['existing_item_id']))
                                    <button 
                                        wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] - 1 }})"
                                        class="w-9 h-9 rounded-md bg-gray-200 hover:bg-gray-300 active:scale-95 flex items-center justify-center touch-manipulation"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                        </svg>
                                    </button>
                                    <span class="w-10 text-center font-bold text-lg">{{ $item['quantity'] }}</span>
                                    <button 
                                        wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] + 1 }})"
                                        class="w-9 h-9 rounded-md bg-blue-500 hover:bg-blue-600 text-white active:scale-95 flex items-center justify-center touch-manipulation"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                @else
                                    <span class="text-sm text-gray-600">{{ $item['quantity'] }}x</span>
                                @endif
                            </div>
                            <span class="font-bold text-lg">${{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                        </div>

                        @if(!isset($item['existing_item_id']))
                            <input 
                                type="text" 
                                wire:model.blur="cart.{{ $index }}.notes"
                                placeholder="Notas especiales..."
                                class="mt-2 w-full px-3 py-2 text-sm border-2 border-gray-200 rounded focus:border-blue-500 focus:ring-0 focus:outline-none transition-colors"
                            >
                        @elseif($item['notes'])
                            <p class="mt-2 text-xs text-gray-600 italic">{{ $item['notes'] }}</p>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Selecciona productos</p>
                    </div>
                @endforelse
            </div>

            <!-- Mobile Cart Footer -->
            <div class="border-t border-gray-200 p-4 space-y-3 flex-shrink-0 bg-white">
                <!-- Notes -->
                <textarea 
                    wire:model="notes"
                    placeholder="Notas generales de la orden..."
                    rows="2"
                    class="w-full px-3 py-2 text-sm border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0 focus:outline-none transition-colors"
                ></textarea>

                <!-- Totals -->
                <div class="space-y-2 bg-gray-50 rounded-lg p-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">${{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>IVA incluido</span>
                        <span>${{ number_format($taxAmount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-xl font-bold pt-2 border-t border-gray-200">
                        <span>Total</span>
                        <span class="text-blue-600">${{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Send Button -->
                <button 
                    wire:click="sendToKitchen"
                    @click="showCart = false"
                    @if(empty($cart)) disabled @endif
                    class="w-full py-4 bg-green-600 hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold rounded-xl active:scale-95 transition-all flex items-center justify-center gap-2 text-lg touch-manipulation shadow-lg"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    @if($order)
                        Agregar a Orden
                    @else
                        Enviar a Cocina
                    @endif
                </button>
            </div>
        </div>
    </div>

    <!-- Modifier Modal -->
    @if($showModifierModal && $selectedProduct)
        <div class="fixed inset-0 bg-black/50 z-[60] flex items-end md:items-center justify-center p-0 md:p-4">
            <div 
                class="bg-white rounded-t-2xl md:rounded-2xl w-full md:max-w-2xl max-h-[90vh] flex flex-col shadow-2xl"
                wire:click.stop
            >
                <!-- Modal Header -->
                <div class="p-4 md:p-6 border-b border-gray-200 flex-shrink-0">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900">{{ $selectedProduct->name }}</h3>
                            <p class="text-2xl font-bold text-blue-600 mt-1">
                                ${{ number_format($selectedProduct->price, 0, ',', '.') }}
                            </p>
                        </div>
                        <button 
                            wire:click="closeModifierModal"
                            class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="flex-1 overflow-y-auto p-4 md:p-6 space-y-6">
                    @foreach($selectedProduct->modifierGroups as $group)
                        <div>
                            <h4 class="text-lg font-bold text-gray-900 mb-3">{{ $group->name }}</h4>
                            <p class="text-sm text-gray-600 mb-3">
                                @if($group->min_selection > 0 && $group->max_selection > 0)
                                    Selecciona entre {{ $group->min_selection }} y {{ $group->max_selection }} opciones
                                @elseif($group->min_selection > 0)
                                    Selecciona al menos {{ $group->min_selection }} {{ Str::plural('opción', $group->min_selection) }}
                                @elseif($group->max_selection > 0)
                                    Selecciona hasta {{ $group->max_selection }} {{ Str::plural('opción', $group->max_selection) }}
                                @else
                                    Selecciona las opciones que desees
                                @endif
                            </p>

                            <div class="space-y-2">
                                @foreach($group->modifiers as $modifier)
                                    @php
                                        $key = $group->id . '_' . $modifier->id;
                                        $isSelected = isset($selectedModifiers[$key]);
                                    @endphp
                                    <button
                                        wire:click="toggleModifier({{ $group->id }}, {{ $modifier->id }}, '{{ $modifier->name }}', {{ $modifier->price }})"
                                        class="w-full flex items-center justify-between p-3 md:p-4 rounded-xl border-2 transition-all active:scale-98 {{ $isSelected ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}"
                                    >
                                        <div class="flex items-center gap-3">
                                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 {{ $isSelected ? 'border-blue-500 bg-blue-500' : 'border-gray-300' }}">
                                                @if($isSelected)
                                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                            <span class="font-medium text-gray-900">{{ $modifier->name }}</span>
                                        </div>
                                        @if($modifier->price > 0)
                                            <span class="text-sm font-bold text-blue-600">+${{ number_format($modifier->price, 0, ',', '.') }}</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <!-- Special Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notas especiales</label>
                        <textarea 
                            wire:model="modifierNotes"
                            rows="3"
                            placeholder="Ej: Sin cebolla, poco picante..."
                            class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0 focus:outline-none transition-colors"
                        ></textarea>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-4 md:p-6 border-t border-gray-200 flex-shrink-0 bg-gray-50">
                    <div class="flex items-center gap-3">
                        <!-- Quantity -->
                        <div class="flex items-center gap-2">
                            <button 
                                wire:click="$set('modifierQuantity', {{ max(1, $modifierQuantity - 1) }})"
                                class="w-10 h-10 rounded-lg bg-gray-200 hover:bg-gray-300 active:scale-95 transition-all flex items-center justify-center"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </button>
                            <span class="w-12 text-center text-xl font-bold">{{ $modifierQuantity }}</span>
                            <button 
                                wire:click="$set('modifierQuantity', {{ $modifierQuantity + 1 }})"
                                class="w-10 h-10 rounded-lg bg-blue-500 hover:bg-blue-600 text-white active:scale-95 transition-all flex items-center justify-center"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Add Button -->
                        <button 
                            wire:click="addWithModifiers"
                            class="flex-1 py-3 md:py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl active:scale-95 transition-all flex items-center justify-center gap-2 shadow-md"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>
                                Agregar
                                @php
                                    $modifiersTotal = collect($selectedModifiers)->sum('price');
                                    $itemTotal = ($selectedProduct->price + $modifiersTotal) * $modifierQuantity;
                                @endphp
                                (${{ number_format($itemTotal, 0, ',', '.') }})
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
