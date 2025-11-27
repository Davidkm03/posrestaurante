<x-layouts.app title="Reporte de Impuestos">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Reporte de Impuestos</h1>
                <p class="text-sm text-gray-500">Resumen de IVA y retenciones</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver
                </a>
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Imprimir
                </button>
            </div>
        </div>
    </x-slot>

    <!-- Filtros -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.reports.taxes') }}" class="flex flex-col md:flex-row gap-4">
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

    <!-- Resumen -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <x-card class="bg-gradient-to-br from-blue-500 to-blue-600 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Base Gravable</p>
                    <p class="text-3xl font-bold mt-1">${{ number_format($summary['total_base'], 0, ',', '.') }}</p>
                </div>
                <div class="p-3 bg-white/20 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </x-card>

        <x-card class="bg-gradient-to-br from-orange-500 to-orange-600 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm">IVA Generado (19%)</p>
                    <p class="text-3xl font-bold mt-1">${{ number_format($summary['total_tax'], 0, ',', '.') }}</p>
                </div>
                <div class="p-3 bg-white/20 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </x-card>

        <x-card class="bg-gradient-to-br from-green-500 to-green-600 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Total con IVA</p>
                    <p class="text-3xl font-bold mt-1">${{ number_format($summary['total_base'] + $summary['total_tax'], 0, ',', '.') }}</p>
                </div>
                <div class="p-3 bg-white/20 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
                    </svg>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Gráfica de pie -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <x-card>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribución de Impuestos</h3>
            <div class="h-64">
                <canvas id="taxChart"></canvas>
            </div>
        </x-card>

        <x-card>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Información Fiscal</h3>
            <div class="space-y-4">
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="font-medium text-yellow-800">Período Fiscal</h4>
                            <p class="text-sm text-yellow-700 mt-1">
                                Del {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h4 class="font-medium text-blue-800 mb-2">Desglose</h4>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-blue-600">Ventas brutas:</dt>
                            <dd class="font-medium text-blue-900">${{ number_format($summary['total_base'] + $summary['total_tax'], 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-blue-600">(-) IVA incluido:</dt>
                            <dd class="font-medium text-blue-900">${{ number_format($summary['total_tax'], 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex justify-between border-t border-blue-200 pt-2">
                            <dt class="text-blue-600 font-semibold">Base gravable:</dt>
                            <dd class="font-bold text-blue-900">${{ number_format($summary['total_base'], 0, ',', '.') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Tabla de impuestos -->
    <x-card>
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Detalle por Tipo de Impuesto</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Porcentaje</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Base Gravable</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Impuesto</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($taxes as $tax)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-sm font-medium bg-orange-100 text-orange-800 rounded-full">{{ $tax['tax_type'] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ $tax['tax_percentage'] }}%</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">${{ number_format($tax['base'], 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">${{ number_format($tax['total_tax'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                No hay datos para el período seleccionado
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-100">
                    <tr>
                        <td colspan="2" class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">TOTAL</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">${{ number_format($summary['total_base'], 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">${{ number_format($summary['total_tax'], 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </x-card>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('taxChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Base Gravable', 'IVA (19%)'],
                datasets: [{
                    data: [{{ $summary['total_base'] }}, {{ $summary['total_tax'] }}],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                    ],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
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
