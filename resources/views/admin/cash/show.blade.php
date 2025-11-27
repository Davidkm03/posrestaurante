<x-layouts.app title="Detalle de Sesión de Caja">
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detalle de Sesión de Caja</h1>
                <p class="text-gray-600">{{ $cashSession->cashRegister->name }} • {{ $cashSession->opened_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.cash.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
                @if($cashSession->status === 'open')
                    <a href="{{ route('admin.cash.close', $cashSession) }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        <i class="fas fa-lock mr-2"></i>Cerrar Caja
                    </a>
                @else
                    <a href="{{ route('admin.cash.report', $cashSession) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-file-pdf mr-2"></i>Ver Reporte
                    </a>
                @endif
            </div>
        </div>

        <!-- Status Badge -->
        <div class="mb-6">
            <span class="px-4 py-2 rounded-full text-sm font-semibold
                {{ $cashSession->status === 'open' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                <i class="fas fa-circle text-xs mr-1"></i>
                {{ $cashSession->status === 'open' ? 'Abierta' : 'Cerrada' }}
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Session Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Session Summary -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Resumen de Sesión</h2>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Caja Registradora</p>
                            <p class="font-semibold text-gray-900">{{ $cashSession->cashRegister->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Usuario</p>
                            <p class="font-semibold text-gray-900">{{ $cashSession->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Apertura</p>
                            <p class="font-semibold text-gray-900">{{ $cashSession->opened_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                        @if($cashSession->closed_at)
                        <div>
                            <p class="text-sm text-gray-600">Cierre</p>
                            <p class="font-semibold text-gray-900">{{ $cashSession->closed_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Cerrada por</p>
                            <p class="font-semibold text-gray-900">{{ $cashSession->closedBy?->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Duración</p>
                            <p class="font-semibold text-gray-900">{{ $cashSession->opened_at->diffForHumans($cashSession->closed_at, true) }}</p>
                        </div>
                        @else
                        <div>
                            <p class="text-sm text-gray-600">Tiempo Abierta</p>
                            <p class="font-semibold text-gray-900">{{ $cashSession->opened_at->diffForHumans() }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Movements -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Movimientos de Caja</h2>
                    </div>

                    @if($cashSession->movements->isEmpty())
                        <p class="text-gray-500 text-center py-8">No hay movimientos registrados</p>
                    @else
                        <div class="space-y-3">
                            @foreach($cashSession->movements as $movement)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center
                                            {{ $movement->type === 'income' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                            <i class="fas {{ $movement->type === 'income' ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $movement->concept }}</p>
                                            <p class="text-sm text-gray-600">{{ $movement->user->name }} • {{ $movement->created_at->format('H:i') }}</p>
                                            @if($movement->notes)
                                                <p class="text-xs text-gray-500 mt-1">{{ $movement->notes }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold {{ $movement->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $movement->type === 'income' ? '+' : '-' }}${{ number_format($movement->amount, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Orders -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Órdenes de la Sesión</h2>

                    @if($cashSession->orders->isEmpty())
                        <p class="text-gray-500 text-center py-8">No hay órdenes registradas</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Orden</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mesa</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hora</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($cashSession->orders as $order)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                                    #{{ $order->order_number }}
                                                </a>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 capitalize">
                                                {{ $order->type?->value ?? $order->type }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                {{ $order->table?->getDisplayName() ?? '-' }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                ${{ number_format($order->total, 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                @php
                                                    $statusValue = $order->status?->value ?? $order->status;
                                                @endphp
                                                <span class="px-2 py-1 text-xs rounded-full
                                                    {{ $statusValue === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ ucfirst($statusValue) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                {{ $order->created_at->format('H:i') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Financial Summary -->
            <div class="space-y-6">
                <!-- Cash Summary -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Resumen Financiero</h2>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Monto Apertura</span>
                            <span class="font-semibold text-gray-900">${{ number_format($cashSession->opening_amount, 0, ',', '.') }}</span>
                        </div>

                        @php
                            $totalSales = $cashSession->orders->where('payment_status', 'paid')->sum('total');
                            $totalIncome = $cashSession->movements->where('type', 'income')->sum('amount');
                            $totalExpense = $cashSession->movements->where('type', 'expense')->sum('amount');
                            $expectedCash = $cashSession->opening_amount + $totalSales + $totalIncome - $totalExpense;
                        @endphp

                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Ventas</span>
                            <span class="font-semibold text-green-600">+${{ number_format($totalSales, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Ingresos Otros</span>
                            <span class="font-semibold text-green-600">+${{ number_format($totalIncome, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Gastos</span>
                            <span class="font-semibold text-red-600">-${{ number_format($totalExpense, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between items-center pt-2">
                            <span class="text-base font-semibold text-gray-900">Efectivo Esperado</span>
                            <span class="text-lg font-bold text-blue-600">${{ number_format($expectedCash, 0, ',', '.') }}</span>
                        </div>

                        @if($cashSession->closed_at)
                            <div class="pt-4 border-t-2 border-gray-300">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-base font-semibold text-gray-900">Efectivo Contado</span>
                                    <span class="text-lg font-bold text-gray-900">${{ number_format($cashSession->closing_amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Diferencia</span>
                                    <span class="font-bold {{ $cashSession->difference >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $cashSession->difference >= 0 ? '+' : '' }}${{ number_format($cashSession->difference, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Payment Methods Breakdown -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Por Método de Pago</h2>
                    
                    @php
                        $paymentsByMethod = $cashSession->orders->load('payments.paymentMethod')
                            ->flatMap->payments
                            ->groupBy('paymentMethod.name')
                            ->map(fn($payments) => $payments->sum('amount'));
                    @endphp

                    @if($paymentsByMethod->isEmpty())
                        <p class="text-gray-500 text-center py-4 text-sm">Sin pagos registrados</p>
                    @else
                        <div class="space-y-3">
                            @foreach($paymentsByMethod as $method => $total)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">{{ $method }}</span>
                                    <span class="font-semibold text-gray-900">${{ number_format($total, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                            <div class="pt-3 border-t border-gray-200 flex justify-between items-center">
                                <span class="text-sm font-semibold text-gray-900">Total</span>
                                <span class="font-bold text-gray-900">${{ number_format($paymentsByMethod->sum(), 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Statistics -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Estadísticas</h2>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Órdenes Totales</span>
                            <span class="font-semibold text-gray-900">{{ $cashSession->orders->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Ticket Promedio</span>
                            <span class="font-semibold text-gray-900">
                                ${{ $cashSession->orders->count() > 0 ? number_format($totalSales / $cashSession->orders->count(), 0, ',', '.') : '0' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Movimientos</span>
                            <span class="font-semibold text-gray-900">{{ $cashSession->movements->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
