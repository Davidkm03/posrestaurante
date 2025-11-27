<x-layouts.app title="Ventas por Hora">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Ventas por Hora</h1>
                <p class="text-sm text-gray-500">Análisis de las horas con mayor actividad</p>
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
        <form method="GET" action="{{ route('admin.reports.hourly') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                <input type="date" name="date" value="{{ $date }}" 
                    class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0 focus:outline-none transition-colors">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Filtrar
                </button>
                <a href="{{ route('admin.reports.hourly', ['date' => now()->toDateString()]) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    Hoy
                </a>
                <a href="{{ route('admin.reports.hourly', ['date' => now()->subDay()->toDateString()]) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    Ayer
                </a>
            </div>
        </form>
    </x-card>

    <!-- Resumen rápido -->
    @php
        $totalOrders = $hours->sum(fn($h) => $h->orders);
        $totalSales = $hours->sum(fn($h) => $h->total);
        $peakHour = $hours->sortByDesc('orders')->first();
        $peakSalesHour = $hours->sortByDesc('total')->first();
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-card>
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Órdenes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalOrders) }}</p>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center gap-3">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Ventas</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($totalSales, 0, ',', '.') }}</p>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center gap-3">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Hora Pico (Órdenes)</p>
                    <p class="text-2xl font-bold text-gray-900">{{ str_pad($peakHour->hour ?? 0, 2, '0', STR_PAD_LEFT) }}:00</p>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center gap-3">
                <div class="p-2 bg-orange-100 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Hora Pico (Ventas)</p>
                    <p class="text-2xl font-bold text-gray-900">{{ str_pad($peakSalesHour->hour ?? 0, 2, '0', STR_PAD_LEFT) }}:00</p>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Gráfica -->
    <x-card class="mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribución por Hora - {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</h3>
        <div class="h-80">
            <canvas id="hourlyChart"></canvas>
        </div>
    </x-card>

    <!-- Tabla detallada -->
    <x-card>
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Detalle por Hora</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Órdenes</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ventas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actividad</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php $maxOrders = $hours->max(fn($h) => $h->orders) ?: 1; @endphp
                    @foreach($hours as $hour => $data)
                        @php
                            $isBusinessHour = $hour >= 7 && $hour <= 23;
                            $activityLevel = $maxOrders > 0 ? ($data->orders / $maxOrders) * 100 : 0;
                        @endphp
                        <tr class="{{ !$isBusinessHour ? 'bg-gray-50' : '' }} hover:bg-blue-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium {{ $data->orders > 0 ? 'text-gray-900' : 'text-gray-400' }}">
                                    {{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}:00 - {{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}:59
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                @if($data->orders > 0)
                                    <span class="font-semibold text-gray-900">{{ number_format($data->orders) }}</span>
                                @else
                                    <span class="text-gray-400">0</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                @if($data->total > 0)
                                    <span class="font-semibold text-gray-900">${{ number_format($data->total, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-gray-400">$0</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 max-w-[150px] bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full transition-all duration-300
                                            {{ $activityLevel >= 75 ? 'bg-green-500' : ($activityLevel >= 50 ? 'bg-yellow-500' : ($activityLevel > 0 ? 'bg-blue-500' : 'bg-gray-300')) }}" 
                                            style="width: {{ $activityLevel }}%">
                                        </div>
                                    </div>
                                    @if($activityLevel >= 75)
                                        <span class="text-xs font-medium text-green-600">Alto</span>
                                    @elseif($activityLevel >= 50)
                                        <span class="text-xs font-medium text-yellow-600">Medio</span>
                                    @elseif($activityLevel > 0)
                                        <span class="text-xs font-medium text-blue-600">Bajo</span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const hoursData = @json($hours->values());
        
        const ctx = document.getElementById('hourlyChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: hoursData.map(h => h.hour.toString().padStart(2, '0') + ':00'),
                datasets: [{
                    label: 'Ventas ($)',
                    data: hoursData.map(h => h.total),
                    backgroundColor: hoursData.map(h => {
                        const hour = h.hour;
                        if (hour >= 12 && hour <= 14) return 'rgba(239, 68, 68, 0.8)'; // Almuerzo
                        if (hour >= 19 && hour <= 21) return 'rgba(245, 158, 11, 0.8)'; // Cena
                        return 'rgba(59, 130, 246, 0.8)';
                    }),
                    borderRadius: 4,
                    yAxisID: 'y',
                }, {
                    label: 'Órdenes',
                    data: hoursData.map(h => h.orders),
                    type: 'line',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    yAxisID: 'y1',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (context.dataset.label.includes('Ventas')) {
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
