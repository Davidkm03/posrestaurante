<x-layouts.app title="Productos">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Productos</h1>
                <p class="text-sm text-gray-500">Gestiona el catálogo de productos</p>
            </div>
            <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nuevo Producto
            </a>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-card class="mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <x-input
                name="search"
                placeholder="Buscar por nombre o SKU..."
                :value="request('search')"
                class="w-64"
            />
            <x-select
                name="category"
                placeholder="Todas las categorías"
                :options="$categories->pluck('name', 'id')->toArray()"
                :selected="request('category')"
                class="w-48"
            />
            <x-select
                name="status"
                placeholder="Todos los estados"
                :options="['active' => 'Activos', 'inactive' => 'Inactivos']"
                :selected="request('status')"
                class="w-40"
            />
            <x-button type="submit" variant="secondary">Filtrar</x-button>
            @if(request()->hasAny(['search', 'category', 'status']))
                <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Limpiar</a>
            @endif
        </form>
    </x-card>

    <!-- Products Table -->
    <x-card :padding="false">
        <x-table>
            <x-slot name="head">
                <x-th>Producto</x-th>
                <x-th>Categoría</x-th>
                <x-th align="right">Precio</x-th>
                <x-th align="center">IVA</x-th>
                <x-th align="center">Estado</x-th>
                <x-th align="right">Acciones</x-th>
            </x-slot>

            @forelse($products as $product)
                <tr>
                    <x-td>
                        <div class="flex items-center gap-3">
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" class="w-10 h-10 rounded-lg object-cover" alt="">
                            @else
                                <div class="w-10 h-10 rounded-lg bg-gray-200 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <p class="font-medium text-gray-900">{{ $product->name }}</p>
                                <p class="text-sm text-gray-500">{{ $product->sku ?? 'Sin SKU' }}</p>
                            </div>
                        </div>
                    </x-td>
                    <x-td>
                        <x-badge type="secondary">{{ $product->category->name ?? 'Sin categoría' }}</x-badge>
                    </x-td>
                    <x-td align="right">
                        <span class="font-semibold">${{ number_format($product->price, 0, ',', '.') }}</span>
                    </x-td>
                    <x-td align="center">
                        {{ $product->tax_percentage }}%
                    </x-td>
                    <x-td align="center">
                        <button
                            onclick="document.getElementById('toggle-form-{{ $product->id }}').submit()"
                            class="inline-flex"
                        >
                            @if($product->is_active)
                                <x-badge type="success" dot>Activo</x-badge>
                            @else
                                <x-badge type="danger" dot>Inactivo</x-badge>
                            @endif
                        </button>
                        <form id="toggle-form-{{ $product->id }}" action="{{ route('admin.products.toggle-status', $product) }}" method="POST" class="hidden">
                            @csrf
                            @method('PATCH')
                        </form>
                    </x-td>
                    <x-td align="right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.products.edit', $product) }}" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este producto?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </x-td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <x-empty-state
                            title="No hay productos"
                            description="Comienza creando tu primer producto"
                        >
                            <x-slot name="action">
                                <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Crear Producto
                                </a>
                            </x-slot>
                        </x-empty-state>
                    </td>
                </tr>
            @endforelse
        </x-table>

        @if($products->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $products->links() }}
            </div>
        @endif
    </x-card>
</x-layouts.app>
