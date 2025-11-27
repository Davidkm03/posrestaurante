<x-layouts.app title="Editar Categoría">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Editar Categoría</h1>
                <p class="text-sm text-gray-500">Actualiza la información de la categoría</p>
            </div>
            <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl">
        <x-card>
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <!-- Nombre -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            value="{{ old('name', $category->name) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                            required
                        >
                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                            Descripción
                        </label>
                        <textarea 
                            name="description" 
                            id="description" 
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror"
                        >{{ old('description', $category->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Color -->
                        <div>
                            <label for="color" class="block text-sm font-medium text-gray-700 mb-1">
                                Color
                            </label>
                            <div class="flex gap-2">
                                <input 
                                    type="color" 
                                    name="color" 
                                    id="color" 
                                    value="{{ old('color', $category->color ?? '#6B7280') }}"
                                    class="h-10 w-20 rounded border border-gray-300 cursor-pointer"
                                >
                                <input 
                                    type="text" 
                                    value="{{ old('color', $category->color ?? '#6B7280') }}"
                                    readonly
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50"
                                    id="color-preview"
                                >
                            </div>
                            @error('color')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Ícono -->
                        <div>
                            <label for="icon" class="block text-sm font-medium text-gray-700 mb-1">
                                Ícono
                            </label>
                            <input 
                                type="text" 
                                name="icon" 
                                id="icon" 
                                value="{{ old('icon', $category->icon) }}"
                                placeholder="appetizer, main-course, etc."
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('icon') border-red-500 @enderror"
                            >
                            @error('icon')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Imagen -->
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-1">
                            Imagen
                        </label>
                        @if($category->image)
                            <div class="mb-2">
                                <img src="{{ Storage::url($category->image) }}" class="w-32 h-32 object-cover rounded-lg">
                            </div>
                        @endif
                        <input 
                            type="file" 
                            name="image" 
                            id="image" 
                            accept="image/*"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('image') border-red-500 @enderror"
                        >
                        @error('image')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Orden -->
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">
                            Orden
                        </label>
                        <input 
                            type="number" 
                            name="sort_order" 
                            id="sort_order" 
                            value="{{ old('sort_order', $category->sort_order ?? 0) }}"
                            min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('sort_order') border-red-500 @enderror"
                        >
                        @error('sort_order')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Menor número = mayor prioridad</p>
                    </div>

                    <!-- Switches -->
                    <div class="space-y-4 pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="is_active" class="text-sm font-medium text-gray-700">Activa</label>
                                <p class="text-xs text-gray-500">La categoría aparecerá en el sistema</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="is_active" value="0">
                                <input 
                                    type="checkbox" 
                                    name="is_active" 
                                    id="is_active" 
                                    value="1"
                                    {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                                    class="sr-only peer"
                                >
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <label for="show_in_pos" class="text-sm font-medium text-gray-700">Mostrar en POS</label>
                                <p class="text-xs text-gray-500">Disponible en el punto de venta</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="show_in_pos" value="0">
                                <input 
                                    type="checkbox" 
                                    name="show_in_pos" 
                                    id="show_in_pos" 
                                    value="1"
                                    {{ old('show_in_pos', $category->show_in_pos) ? 'checked' : '' }}
                                    class="sr-only peer"
                                >
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <label for="show_in_menu" class="text-sm font-medium text-gray-700">Mostrar en Menú</label>
                                <p class="text-xs text-gray-500">Visible para clientes</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="show_in_menu" value="0">
                                <input 
                                    type="checkbox" 
                                    name="show_in_menu" 
                                    id="show_in_menu" 
                                    value="1"
                                    {{ old('show_in_menu', $category->show_in_menu) ? 'checked' : '' }}
                                    class="sr-only peer"
                                >
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-between gap-4 pt-6 mt-6 border-t border-gray-200">
                    <button 
                        type="button"
                        onclick="if(confirm('¿Estás seguro de eliminar esta categoría?')) { document.getElementById('delete-form').submit(); }"
                        class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg"
                    >
                        Eliminar
                    </button>
                    <div class="flex gap-3">
                        <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            Cancelar
                        </a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Guardar Cambios
                        </button>
                    </div>
                </div>
            </form>

            <!-- Formulario de eliminación -->
            <form id="delete-form" action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </x-card>
    </div>

    @push('scripts')
    <script>
        // Sincronizar color picker con input de texto
        const colorInput = document.getElementById('color');
        const colorPreview = document.getElementById('color-preview');
        
        colorInput.addEventListener('input', function() {
            colorPreview.value = this.value.toUpperCase();
        });
    </script>
    @endpush
</x-layouts.app>
