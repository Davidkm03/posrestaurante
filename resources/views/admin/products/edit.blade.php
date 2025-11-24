<x-layouts.app title="Editar Producto">
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.products.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Editar Producto</h1>
                <p class="text-sm text-gray-500">{{ $product->name }}</p>
            </div>
        </div>
    </x-slot>

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <x-card title="Información Básica">
                    <div class="space-y-4">
                        <x-input name="name" label="Nombre del producto" required :value="$product->name" />

                        <div class="grid grid-cols-2 gap-4">
                            <x-select name="category_id" label="Categoría" required :options="$categories->pluck('name', 'id')->toArray()" :selected="$product->category_id" />
                            <x-input name="sku" label="SKU" :value="$product->sku" />
                        </div>

                        <x-textarea name="description" label="Descripción" rows="3" :value="$product->description" />
                    </div>
                </x-card>

                <x-card title="Precios e Impuestos">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <x-input type="number" name="price" label="Precio de venta" required prefix="$" step="100" :value="$product->price" />
                            <x-input type="number" name="cost" label="Costo" prefix="$" step="100" :value="$product->cost" />
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <x-select name="tax_type" label="Tipo de impuesto" required :options="['01' => 'IVA', '04' => 'Impoconsumo', 'ZZ' => 'Exento']" :selected="$product->tax_type" />
                            <x-input type="number" name="tax_percentage" label="% Impuesto" required suffix="%" :value="$product->tax_percentage" />
                            <div class="flex items-end pb-2">
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="tax_included" value="1" @checked($product->tax_included) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-gray-700">IVA incluido</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </x-card>

                <x-card title="Modificadores">
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($modifierGroups as $group)
                            <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" name="modifier_groups[]" value="{{ $group->id }}"
                                    @checked($product->modifierGroups->contains($group->id))
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $group->name }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </x-card>
            </div>

            <div class="space-y-6">
                <x-card title="Imagen">
                    <div class="space-y-4">
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center" id="image-preview-container">
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" class="w-full h-40 object-cover rounded-lg">
                                <button type="button" onclick="clearImage()" class="mt-2 text-sm text-red-600 hover:text-red-800">Cambiar</button>
                            @else
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <button type="button" onclick="document.getElementById('image-input').click()" class="mt-3 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100">
                                    Seleccionar imagen
                                </button>
                            @endif
                        </div>
                        <input type="file" name="image" accept="image/*" class="hidden" id="image-input" onchange="previewImage(this)">
                    </div>
                </x-card>

                <x-card title="Configuración">
                    <div class="space-y-4">
                        <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-700">Producto activo</span>
                            <input type="checkbox" name="is_active" value="1" @checked($product->is_active) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </label>

                        <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-700">Mostrar en POS</span>
                            <input type="checkbox" name="show_in_pos" value="1" @checked($product->show_in_pos) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </label>

                        <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-700">Permitir notas</span>
                            <input type="checkbox" name="allow_notes" value="1" @checked($product->allow_notes) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </label>
                    </div>
                </x-card>

                <div class="flex gap-3">
                    <a href="{{ route('admin.products.index') }}" class="flex-1 px-4 py-2 text-center text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
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
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const container = document.getElementById('image-preview-container');
                    container.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-40 object-cover rounded-lg">
                        <button type="button" onclick="clearImage()" class="mt-2 text-sm text-red-600">Cambiar</button>
                    `;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function clearImage() {
            document.getElementById('image-input').click();
        }
    </script>
    @endpush
</x-layouts.app>
