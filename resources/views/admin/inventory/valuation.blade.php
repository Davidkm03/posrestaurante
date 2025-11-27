<x-layouts.app title="Valorización de Inventario">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Valorización de Inventario</h1>
                <p class="text-sm text-gray-500">Valor monetario del inventario actual</p>
            </div>
            <a href="{{ route('admin.inventory.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <!-- Totales -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-500">Total Productos</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($totals['products']) }}</p>
                <p class="text-xs text-gray-400">con control de inventario</p>
            </div>
        </x-card>

        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-500">Unidades en Stock</p>
                <p class="text-3xl font-bold text-gray-900">{{ number_format($totals['total_stock']) }}</p>
                <p class="text-xs text-gray-400">unidades totales</p>
            </div>
        </x-card>

        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-500">Valor a Costo</p>
                <p class="text-3xl font-bold text-blue-600">${{ number_format($totals['cost_value'], 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400">precio de compra</p>
            </div>
        </x-card>

        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-500">Valor a Precio Venta</p>
                <p class="text-3xl font-bold text-green-600">${{ number_format($totals['sale_value'], 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400">potencial de venta</p>
            </div>
        </x-card>
    </div>

    <!-- Gráfica y tabla -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Gráfica por categoría -->
        <x-card title="Valor por Categoría">
            <div class="h-64">
                <canvas id="categoryChart"></canvas>
            </div>
        </x-card>

        <!-- Tabla de categorías -->
        <x-card title="Detalle por Categoría">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Productos</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stock</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Valor Costo</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($categories as $categoryName => $data)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $categoryName ?: 'Sin categoría' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-500">
                                    {{ number_format($data['count']) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-500">
                                    {{ number_format($data['total_stock']) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-medium text-gray-900">
                                    ${{ number_format($data['cost_value'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                    No hay datos de inventario
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-100">
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900">Total</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-bold text-gray-900">
                                {{ number_format($totals['products']) }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-bold text-gray-900">
                                {{ number_format($totals['total_stock']) }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-bold text-gray-900">
                                ${{ number_format($totals['cost_value'], 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-card>
    </div>

    <!-- Margen potencial -->
    <x-card class="mt-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Margen Potencial</h3>
                <p class="text-sm text-gray-500">Diferencia entre valor de venta y costo</p>
            </div>
            <div class="text-right">
                <p class="text-3xl font-bold text-purple-600">
                    ${{ number_format($totals['sale_value'] - $totals['cost_value'], 0, ',', '.') }}
                </p>
                @if($totals['cost_value'] > 0)
                    <p class="text-sm text-gray-500">
                        {{ number_format((($totals['sale_value'] - $totals['cost_value']) / $totals['cost_value']) * 100, 1) }}% de margen
                    </p>
                @endif
            </div>
        </div>
    </x-card>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const categories = @json($categories);
        const labels = Object.keys(categories);
        const costValues = Object.values(categories).map(c => c.cost_value);
        const saleValues = Object.values(categories).map(c => c.sale_value);

        const ctx = document.getElementById('categoryChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels.map(l => l || 'Sin categoría'),
                datasets: [{
                    data: costValues,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(20, 184, 166, 0.8)',
                        'rgba(251, 146, 60, 0.8)',
                    ],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': $' + context.raw.toLocaleString('es-CO');
                            }
                        }
                    }
                }
            }
        });
    </script>
    @endpush
</x-layouts.app>
