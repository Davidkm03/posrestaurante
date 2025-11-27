<x-layouts.app title="Editar Combo">
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.combos.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Editar Combo</h1>
                <p class="text-sm text-gray-500">{{ $combo->name }}</p>
            </div>
        </div>
    </x-slot>

    <form action="{{ route('admin.combos.update', $combo) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <x-card title="Información del Combo">
                    <div class="space-y-4">
                        <x-input name="name" label="Nombre del combo" required 
                            placeholder="Ej: Combo Familiar, Combo Pareja..." 
                            value="{{ old('name', $combo->name) }}" />

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Categoría *</label>
                                <select name="category_id" id="category_id" required
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Seleccionar categoría</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $combo->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <x-input type="number" name="price" label="Precio del combo" required 
                                prefix="$" step="100" min="0" placeholder="0" 
                                value="{{ old('price', $combo->price) }}" />
                        </div>

                        <x-textarea name="description" label="Descripción" rows="2" 
                            placeholder="Descripción opcional del combo..."
                            value="{{ old('description', $combo->description) }}" />
                    </div>
                </x-card>

                <x-card title="Productos del Combo">
                    <p class="text-sm text-gray-500 mb-4">Selecciona los productos que incluirá este combo (mínimo 2)</p>

                    <!-- Buscador -->
                    <div class="mb-4">
                        <input type="text" id="searchProducts" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Buscar productos...">
                    </div>

                    @if($products->isEmpty())
                        <div class="text-center py-8 text-gray-400">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <p class="mt-2">No hay productos disponibles</p>
                        </div>
                    @else
                        <div class="border border-gray-200 rounded-lg max-h-80 overflow-y-auto">
                            <div class="divide-y divide-gray-200" id="productsList">
                                @foreach($products as $product)
                                    <label class="flex items-center gap-3 p-3 hover:bg-gray-50 cursor-pointer product-item">
                                        <input type="checkbox" name="products[]" value="{{ $product->id }}" 
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 product-checkbox"
                                            data-price="{{ $product->price }}"
                                            {{ in_array($product->id, old('products', $selectedProducts)) ? 'checked' : '' }}>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-900 product-name">{{ $product->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $product->category->name ?? 'Sin categoría' }}</p>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">${{ number_format($product->price, 0) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </x-card>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <x-card title="Resumen">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Productos seleccionados</span>
                            <span id="selectedCount" class="font-bold text-blue-600">{{ count($selectedProducts) }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-600">Valor individual</span>
                            <span id="totalValue" class="font-bold text-gray-900">$0</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <span class="text-sm text-green-700">Ahorro cliente</span>
                            <span id="savings" class="font-bold text-green-600">$0</span>
                        </div>
                    </div>
                </x-card>

                <x-card title="Imagen">
                    <div class="space-y-4">
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center" id="image-preview-container">
                            @if($combo->image)
                                <img src="{{ Storage::url($combo->image) }}" class="w-full h-40 object-cover rounded-lg">
                                <button type="button" onclick="clearImage()" class="mt-2 text-sm text-red-600 hover:text-red-800">Eliminar</button>
                            @else
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">PNG, JPG hasta 2MB</p>
                                <button type="button" onclick="document.getElementById('image-input').click()" class="mt-3 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100">
                                    Seleccionar imagen
                                </button>
                            @endif
                            <input type="file" name="image" accept="image/*" class="hidden" id="image-input" onchange="previewImage(this)">
                        </div>
                    </div>
                </x-card>

                <x-card title="Configuración">
                    <div class="space-y-4">
                        <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-700">Combo activo</span>
                            <input type="checkbox" name="is_active" value="1" 
                                {{ old('is_active', $combo->is_active) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </label>

                        <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-700">Mostrar en POS</span>
                            <input type="checkbox" name="show_in_pos" value="1" 
                                {{ old('show_in_pos', $combo->show_in_pos) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </label>
                    </div>
                </x-card>

                <div class="flex gap-3">
                    <a href="{{ route('admin.combos.index') }}" class="flex-1 px-4 py-2 text-center text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancelar
                    </a>
                    <x-button type="submit" variant="primary" class="flex-1 justify-center">
                        Actualizar
                    </x-button>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchProducts');
            const productItems = document.querySelectorAll('.product-item');
            const checkboxes = document.querySelectorAll('.product-checkbox');
            const selectedCount = document.getElementById('selectedCount');
            const totalValue = document.getElementById('totalValue');
            const savings = document.getElementById('savings');
            const priceInput = document.querySelector('input[name="price"]');

            // Búsqueda de productos
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase();
                    productItems.forEach(item => {
                        const name = item.querySelector('.product-name').textContent.toLowerCase();
                        item.style.display = name.includes(query) ? '' : 'none';
                    });
                });
            }

            // Actualizar resumen
            function updateSummary() {
                let count = 0;
                let total = 0;

                checkboxes.forEach(cb => {
                    if (cb.checked) {
                        count++;
                        total += parseInt(cb.dataset.price) || 0;
                    }
                });

                if (selectedCount) selectedCount.textContent = count;
                if (totalValue) totalValue.textContent = '$' + total.toLocaleString('es-CO');

                const comboPrice = parseInt(priceInput?.value) || 0;
                const savingsVal = total - comboPrice;
                if (savings) {
                    savings.textContent = savingsVal > 0 ? '$' + savingsVal.toLocaleString('es-CO') : '$0';
                }
            }

            checkboxes.forEach(cb => cb.addEventListener('change', updateSummary));
            if (priceInput) priceInput.addEventListener('input', updateSummary);
            
            // Inicializar
            updateSummary();
        });

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const container = document.getElementById('image-preview-container');
                    container.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-40 object-cover rounded-lg">
                        <button type="button" onclick="clearImage()" class="mt-2 text-sm text-red-600 hover:text-red-800">Eliminar</button>
                        <input type="file" name="image" accept="image/*" class="hidden" id="image-input" onchange="previewImage(this)">
                    `;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function clearImage() {
            const container = document.getElementById('image-preview-container');
            container.innerHTML = `
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p class="mt-2 text-sm text-gray-500">PNG, JPG hasta 2MB</p>
                <input type="file" name="image" accept="image/*" class="hidden" id="image-input" onchange="previewImage(this)">
                <button type="button" onclick="document.getElementById('image-input').click()" class="mt-3 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100">
                    Seleccionar imagen
                </button>
            `;
        }
    </script>
    @endpush
</x-layouts.app>
