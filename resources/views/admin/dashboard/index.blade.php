<x-layouts.app title="Dashboard">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                <p class="text-sm text-gray-500">Resumen del día {{ now()->format('d/m/Y') }}</p>
            </div>
            <div class="flex items-center gap-3">
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
        <!-- Cash Session -->
        <div class="lg:col-span-2">
            <x-card title="Estado de Caja">
                @if($currentCashSession)
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                            <div>
                                <p class="font-medium text-green-900">Caja Abierta</p>
                                <p class="text-sm text-green-700">
                                    {{ $currentCashSession->cashRegister->name }} -
                                    Abierta por {{ $currentCashSession->openedBy->name }}
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
                        <a href="{{ route('admin.cash.index') }}" class="text-yellow-600 hover:text-yellow-800 text-sm font-medium">
                            Abrir caja
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
                        @foreach($topProducts as $product)
                            <div class="flex items-center justify-between py-2 border-b last:border-0">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-sm font-medium text-gray-600">
                                        {{ $loop->iteration }}
                                    </span>
                                    <span class="font-medium text-gray-900">{{ $product->name }}</span>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium text-gray-900">${{ number_format($product->total, 0, ',', '.') }}</p>
                                    <p class="text-sm text-gray-500">{{ $product->quantity }} unidades</p>
                                </div>
                            </div>
                        @endforeach
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
</x-layouts.app>
