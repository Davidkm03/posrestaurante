<x-layouts.app title="Reporte de Meseros">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Rendimiento de Meseros</h1>
                <p class="text-sm text-gray-500">Análisis de ventas y desempeño por mesero</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Filtros -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.reports.waiters') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                <input type="date" name="start_date" value="{{ $startDate }}" 
                    class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0 focus:outline-none transition-colors">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                <input type="date" name="end_date" value="{{ $endDate }}" 
                    class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0 focus:outline-none transition-colors">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full md:w-auto px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Filtrar
                </button>
            </div>
        </form>
    </x-card>

    <!-- Gráfica -->
    <x-card class="mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Comparativa de Ventas por Mesero</h3>
        <div class="h-80">
            <canvas id="waitersChart"></canvas>
        </div>
    </x-card>

    <!-- Tabla de meseros -->
    <x-card>
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Detalle por Mesero</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mesero</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Órdenes</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Ventas</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket Promedio</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Rendimiento</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php $maxTotal = $waiters->max('total') ?: 1; @endphp
                    @forelse($waiters as $index => $waiter)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr($waiter->waiter->name ?? 'N', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $waiter->waiter->name ?? 'Sin asignar' }}</p>
                                        <p class="text-xs text-gray-500">{{ $waiter->waiter->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">{{ number_format($waiter->orders) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-semibold">${{ number_format($waiter->total, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">${{ number_format($waiter->avg_ticket, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                @php $performance = ($waiter->total / $maxTotal) * 100; @endphp
                                <div class="flex items-center justify-end gap-2">
                                    <div class="w-20 bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full {{ $performance >= 75 ? 'bg-green-500' : ($performance >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                             style="width: {{ $performance }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium {{ $performance >= 75 ? 'text-green-600' : ($performance >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ number_format($performance, 0) }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                No hay datos para el período seleccionado
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const waiters = @json($waiters);
        
        const ctx = document.getElementById('waitersChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: waiters.map(w => w.waiter?.name || 'Sin asignar'),
                datasets: [{
                    label: 'Ventas Totales',
                    data: waiters.map(w => w.total),
                    backgroundColor: 'rgba(139, 92, 246, 0.8)',
                    borderRadius: 8,
                    yAxisID: 'y',
                }, {
                    label: 'Número de Órdenes',
                    data: waiters.map(w => w.orders),
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderRadius: 8,
                    yAxisID: 'y1',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (context.dataset.label === 'Ventas Totales') {
                                    return 'Ventas: $' + context.raw.toLocaleString('es-CO');
                                }
                                return 'Órdenes: ' + context.raw;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString('es-CO');
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });
    </script>
    @endpush
</x-layouts.app>
