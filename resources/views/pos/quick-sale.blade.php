<x-layouts.pos title="Venta Rápida">
    <div class="flex-1 flex overflow-hidden">
        <!-- Left Panel - Products -->
        <div class="w-2/3 flex flex-col bg-gray-900">
            <!-- Header -->
            <div class="flex-shrink-0 bg-gray-800 border-b border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('pos.index') }}" class="text-gray-400 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </a>
                        <div>
                            <h2 class="text-xl font-bold text-white">Venta Rápida</h2>
                            <p class="text-sm text-gray-400">Sin asignación de mesa</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category Tabs -->
            <div class="flex-shrink-0 bg-gray-800 border-b border-gray-700">
                <div class="flex items-center gap-1 p-2 overflow-x-auto scrollbar-hide">
                    <button class="px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white" data-category="all">
                        Todos
                    </button>
                    @foreach($categories as $category)
                    <button class="px-4 py-2 text-sm font-medium rounded-lg text-gray-300 hover:bg-gray-700" data-category="{{ $category->id }}">
                        {{ $category->name }}
                    </button>
                    @endforeach
                </div>
            </div>

            <!-- Products Grid -->
            <div class="flex-1 overflow-y-auto p-4">
                <div class="grid grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4" id="products-grid">
                    @foreach($categories as $category)
                        @foreach($category->products as $product)
                        <button
                            class="pos-product-card bg-gray-800 rounded-xl p-4 flex flex-col items-center hover:bg-gray-700 transition-all border-2 border-transparent hover:border-blue-500"
                            data-category="{{ $category->id }}"
                        >
                            @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-16 h-16 object-cover rounded-lg mb-2">
                            @else
                            <div class="w-16 h-16 bg-gray-700 rounded-lg mb-2 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            @endif
                            <span class="text-sm font-medium text-white text-center line-clamp-2 mb-1">{{ $product->name }}</span>
                            <span class="text-lg font-bold text-blue-400">${{ number_format($product->price, 0, ',', '.') }}</span>
                        </button>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Panel - Order -->
        <div class="w-1/3 flex flex-col bg-gray-800 border-l border-gray-700">
            <!-- Order Header -->
            <div class="flex-shrink-0 p-4 border-b border-gray-700">
                <h3 class="text-lg font-bold text-white">Orden de Venta Rápida</h3>
                <p class="text-sm text-gray-400">Nueva orden</p>
            </div>

            <!-- Order Items -->
            <div class="flex-1 overflow-y-auto p-4">
                <div id="order-items" class="space-y-2">
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p>Agrega productos a la orden</p>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="flex-shrink-0 border-t border-gray-700 p-4 space-y-3">
                <div class="flex justify-between text-gray-400">
                    <span>Subtotal:</span>
                    <span id="subtotal">$0</span>
                </div>
                <div class="flex justify-between text-gray-400">
                    <span>Impuestos:</span>
                    <span id="tax">$0</span>
                </div>
                <div class="flex justify-between text-xl font-bold text-white border-t border-gray-600 pt-3">
                    <span>Total:</span>
                    <span id="total">$0</span>
                </div>

                <!-- Action Buttons -->
                <div class="grid grid-cols-2 gap-2 mt-4">
                    <button class="pos-btn bg-gray-600 hover:bg-gray-700 text-white rounded-lg py-3">
                        Limpiar
                    </button>
                    <button class="pos-btn bg-green-600 hover:bg-green-700 text-white rounded-lg py-3">
                        Cobrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Filter products by category
        document.querySelectorAll('[data-category]').forEach(btn => {
            btn.addEventListener('click', function() {
                const category = this.dataset.category;
                
                // Update active button
                document.querySelectorAll('[data-category]').forEach(b => {
                    b.classList.remove('bg-blue-600', 'text-white');
                    b.classList.add('text-gray-300');
                });
                this.classList.add('bg-blue-600', 'text-white');
                this.classList.remove('text-gray-300');

                // Filter products
                document.querySelectorAll('.pos-product-card').forEach(card => {
                    if (category === 'all' || card.dataset.category === category) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>
</x-layouts.pos>
