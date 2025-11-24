<x-layouts.app title="Categorías">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Categorías</h1>
                <p class="text-sm text-gray-500">Organiza tus productos por categorías</p>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nueva Categoría
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($categories as $category)
            <x-card :padding="false" class="overflow-hidden">
                <div class="h-2" style="background-color: {{ $category->color ?? '#6B7280' }}"></div>
                <div class="p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            @if($category->image)
                                <img src="{{ Storage::url($category->image) }}" class="w-12 h-12 rounded-lg object-cover">
                            @else
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: {{ $category->color ?? '#6B7280' }}20">
                                    <svg class="w-6 h-6" style="color: {{ $category->color ?? '#6B7280' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $category->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $category->products_count }} productos</p>
                            </div>
                        </div>
                        @if($category->is_active)
                            <x-badge type="success" size="sm">Activa</x-badge>
                        @else
                            <x-badge type="danger" size="sm">Inactiva</x-badge>
                        @endif
                    </div>

                    @if($category->description)
                        <p class="mt-3 text-sm text-gray-500 line-clamp-2">{{ $category->description }}</p>
                    @endif

                    <div class="mt-4 flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="flex items-center gap-1">
                            <form action="{{ route('admin.categories.toggle-status', $category) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg" title="Cambiar estado">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('¿Eliminar esta categoría?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </x-card>
        @empty
            <div class="col-span-full">
                <x-empty-state title="No hay categorías" description="Crea tu primera categoría para organizar productos">
                    <x-slot name="action">
                        <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Crear Categoría
                        </a>
                    </x-slot>
                </x-empty-state>
            </div>
        @endforelse
    </div>

    @if($categories->hasPages())
        <div class="mt-6">
            {{ $categories->links() }}
        </div>
    @endif
</x-layouts.app>
