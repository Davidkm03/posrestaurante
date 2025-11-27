<x-layouts.app title="Dashboard">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                <p class="text-sm text-gray-500">Resumen del día {{ now()->format('d/m/Y') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Reportes
                </a>
                <a href="{{ route('pos.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Ir al POS
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-stat-card
            title="Ventas del Día"
            :value="'$' . number_format($todaySales, 0, ',', '.')"
            color="green"
        >
            <x-slot name="icon">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </x-slot>
        </x-stat-card>

        <x-stat-card
            title="Órdenes Hoy"
            :value="$todayOrders"
            color="blue"
        >
            <x-slot name="icon">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </x-slot>
        </x-stat-card>

        <x-stat-card
            title="Órdenes Pendientes"
            :value="$pendingOrders"
            color="yellow"
        >
            <x-slot name="icon">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </x-slot>
        </x-stat-card>

        <x-stat-card
            title="Ticket Promedio"
            :value="'$' . number_format($avgTicket, 0, ',', '.')"
            color="purple"
        >
            <x-slot name="icon">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                </svg>
            </x-slot>
        </x-stat-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sales Chart + Cash Session -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Gráfica de Ventas por Hora -->
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Ventas por Hora</h3>
                    <span class="text-sm text-gray-500">Hoy</span>
                </div>
                <div class="h-64">
                    <canvas id="salesByHourChart"></canvas>
                </div>
            </x-card>

            <!-- Cash Session -->
            <x-card title="Estado de Caja">
                @if($currentCashSession)
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                            <div>
                                <p class="font-medium text-green-900">Caja Abierta</p>
                                <p class="text-sm text-green-700">
                                    {{ $currentCashSession->cashRegister->name }} -
                                    Abierta por {{ $currentCashSession->user->name }}
                                    a las {{ $currentCashSession->opened_at->format('H:i') }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('admin.cash.show', $currentCashSession) }}" class="text-green-600 hover:text-green-800 text-sm font-medium">
                            Ver detalles
                        </a>
                    </div>
                @else
                    <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                            <div>
                                <p class="font-medium text-yellow-900">Caja Cerrada</p>
                                <p class="text-sm text-yellow-700">No hay sesión de caja activa</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.cash.sessions') }}" class="text-yellow-600 hover:text-yellow-800 text-sm font-medium">
                            Ver sesiones
                        </a>
                    </div>
                @endif
            </x-card>

            <!-- Top Products -->
            <x-card title="Productos Más Vendidos" class="mt-6">
                @if($topProducts->isEmpty())
                    <x-empty-state
                        title="Sin datos"
                        description="No hay ventas registradas hoy"
                    />
                @else
                    <div class="space-y-3">
                        @php $maxQuantity = $topProducts->max('quantity') ?: 1; @endphp
                        @foreach($topProducts as $product)
                            <div class="flex items-center justify-between py-2 border-b last:border-0">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-sm font-bold text-white shadow">
                                        {{ $loop->iteration }}
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <span class="font-medium text-gray-900 block truncate">{{ $product->name }}</span>
                                        <div class="flex items-center gap-2 mt-1">
                                            <div class="flex-1 max-w-[100px] bg-gray-200 rounded-full h-1.5">
                                                <div class="h-1.5 rounded-full bg-blue-500" style="width: {{ ($product->quantity / $maxQuantity) * 100 }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-500">{{ $product->quantity }} uds</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right pl-4">
                                    <p class="font-bold text-gray-900">${{ number_format($product->total, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-4 border-t">
                        <a href="{{ route('admin.reports.products') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center gap-1">
                            Ver reporte completo
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                @endif
            </x-card>
        </div>

        <!-- Recent Orders -->
        <div>
            <x-card title="Órdenes Recientes">
                @if($recentOrders->isEmpty())
                    <x-empty-state
                        title="Sin órdenes"
                        description="No hay órdenes recientes"
                    />
                @else
                    <div class="space-y-3">
                        @foreach($recentOrders as $order)
                            <a href="{{ route('admin.orders.show', $order) }}" class="block p-3 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $order->order_number }}</p>
                                        <p class="text-sm text-gray-500">
                                            @if($order->table)
                                                Mesa {{ $order->table->number }}
                                            @else
                                                {{ $order->type }}
                                            @endif
                                            • {{ $order->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium text-gray-900">${{ number_format($order->total, 0, ',', '.') }}</p>
                                        <x-badge :type="$order->status_color">
                                            {{ $order->status_label }}
                                        </x-badge>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Datos de ventas por hora
        const salesByHour = @json($salesByHour);
        const hoursLabels = [];
        const salesData = [];
        const ordersData = [];
        
        // Generar datos para las 24 horas
        for (let i = 0; i < 24; i++) {
            hoursLabels.push(i.toString().padStart(2, '0') + ':00');
            const hourData = salesByHour[i] || { total: 0, count: 0 };
            salesData.push(hourData.total || 0);
            ordersData.push(hourData.count || 0);
        }

        const ctx = document.getElementById('salesByHourChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: hoursLabels,
                datasets: [{
                    label: 'Ventas ($)',
                    data: salesData,
                    backgroundColor: salesData.map((value, index) => {
                        // Resaltar horas pico típicas
                        if (index >= 12 && index <= 14) return 'rgba(239, 68, 68, 0.8)'; // Almuerzo
                        if (index >= 19 && index <= 21) return 'rgba(245, 158, 11, 0.8)'; // Cena
                        return 'rgba(59, 130, 246, 0.8)';
                    }),
                    borderRadius: 4,
                    yAxisID: 'y',
                }, {
                    label: 'Órdenes',
                    data: ordersData,
                    type: 'line',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    yAxisID: 'y1',
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    fill: true,
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
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45,
                            font: { size: 10 }
                        }
                    },
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
