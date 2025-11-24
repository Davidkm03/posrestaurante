<x-layouts.app title="Crear Categoría">
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.categories.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Crear Categoría</h1>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <x-card>
                <div class="space-y-4">
                    <x-input name="name" label="Nombre de la categoría" required placeholder="Ej: Hamburguesas" />

                    <x-textarea name="description" label="Descripción" rows="2" />

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                            <input type="color" name="color" value="#3B82F6" class="w-full h-10 rounded-lg border border-gray-300 cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Icono</label>
                            <x-select name="icon" :options="[
                                'utensils' => 'Cubiertos',
                                'pizza' => 'Pizza',
                                'burger' => 'Hamburguesa',
                                'coffee' => 'Café',
                                'beer' => 'Bebidas',
                                'cake' => 'Postres',
                                'salad' => 'Ensaladas',
                            ]" placeholder="Seleccionar icono" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Imagen</label>
                        <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
                    </div>

                    <div class="flex gap-4 pt-4">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-blue-600">
                            <span class="text-sm text-gray-700">Activa</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="show_in_pos" value="1" checked class="rounded border-gray-300 text-blue-600">
                            <span class="text-sm text-gray-700">Mostrar en POS</span>
                        </label>
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</a>
                        <x-button type="submit">Guardar</x-button>
                    </div>
                </x-slot>
            </x-card>
        </form>
    </div>
</x-layouts.app>
