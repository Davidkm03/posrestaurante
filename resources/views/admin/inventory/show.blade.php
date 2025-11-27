<x-layouts.app title="Historial de {{ $product->name }}">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                    <a href="{{ route('admin.inventory.index') }}" class="hover:text-blue-600">Inventario</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span>{{ $product->name }}</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
                <p class="text-sm text-gray-500">SKU: {{ $product->sku ?? 'Sin SKU' }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.inventory.adjust', $product) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Ajustar Stock
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Info del producto -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">
        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-500">Stock Actual</p>
                @php
                    $stockStatus = $product->stock <= 0 ? 'out' : ($product->stock <= $product->min_stock ? 'low' : 'ok');
                @endphp
                <p class="text-3xl font-bold {{ $stockStatus === 'out' ? 'text-red-600' : ($stockStatus === 'low' ? 'text-yellow-600' : 'text-green-600') }}">
                    {{ number_format($product->stock, $product->unit === 'unidad' ? 0 : 2) }}
                </p>
                <p class="text-xs text-gray-400">{{ $product->unit }}</p>
            </div>
        </x-card>

        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-500">Stock Mínimo</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($product->min_stock) }}</p>
                <p class="text-xs text-gray-400">Alerta cuando baje</p>
            </div>
        </x-card>

        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-500">Costo Unitario</p>
                <p class="text-3xl font-bold text-gray-900">${{ number_format($product->cost, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400">Por {{ $product->unit }}</p>
            </div>
        </x-card>

        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-500">Valor en Stock</p>
                <p class="text-3xl font-bold text-gray-900">${{ number_format($product->stock * $product->cost, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400">Stock × Costo</p>
            </div>
        </x-card>
    </div>

    <!-- Historial de movimientos -->
    <x-card title="Historial de Movimientos">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Antes</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Después</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notas</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($movements as $movement)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $movement->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full 
                                    {{ $movement->type->isIncoming() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $movement->type->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium {{ $movement->type->isIncoming() ? 'text-green-600' : 'text-red-600' }}">
                                {{ $movement->type->isIncoming() ? '+' : '-' }}{{ number_format($movement->quantity, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">
                                {{ number_format($movement->stock_before, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">
                                {{ number_format($movement->stock_after, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $movement->user->name ?? 'Sistema' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $movement->notes }}">
                                {{ $movement->notes ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p class="mt-2">No hay movimientos registrados</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($movements->hasPages())
            <div class="mt-4 px-4">
                {{ $movements->links() }}
            </div>
        @endif
    </x-card>
</x-layouts.app>
