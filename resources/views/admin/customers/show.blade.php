<x-layouts.app title="Cliente: {{ $customer->name }}">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.customers.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $customer->name }}</h1>
                    <p class="text-sm text-gray-500">{{ $customer->document_type }} {{ $customer->document_number }}</p>
                </div>
            </div>
            <a href="{{ route('admin.customers.edit', $customer) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Editar
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información del Cliente -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Perfil -->
            <x-card>
                <div class="text-center mb-6">
                    <div class="w-20 h-20 mx-auto rounded-full bg-blue-100 flex items-center justify-center mb-4">
                        <span class="text-3xl font-bold text-blue-600">{{ substr($customer->name, 0, 1) }}</span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $customer->name }}</h2>
                    <x-badge type="{{ $customer->customer_type === 'business' ? 'purple' : 'secondary' }}">
                        {{ $customer->customer_type === 'business' ? 'Empresa' : 'Persona Natural' }}
                    </x-badge>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center gap-3 text-gray-600">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                        </svg>
                        <div>
                            <p class="text-xs text-gray-400">Documento</p>
                            <p class="font-medium">{{ $customer->document_type }} {{ $customer->document_number }}</p>
                        </div>
                    </div>

                    @if($customer->email)
                        <div class="flex items-center gap-3 text-gray-600">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <p class="text-xs text-gray-400">Email</p>
                                <p class="font-medium">{{ $customer->email }}</p>
                            </div>
                        </div>
                    @endif

                    @if($customer->phone)
                        <div class="flex items-center gap-3 text-gray-600">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <div>
                                <p class="text-xs text-gray-400">Teléfono</p>
                                <p class="font-medium">{{ $customer->phone }}</p>
                            </div>
                        </div>
                    @endif

                    @if($customer->address)
                        <div class="flex items-center gap-3 text-gray-600">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <div>
                                <p class="text-xs text-gray-400">Dirección</p>
                                <p class="font-medium">{{ $customer->address }}</p>
                                @if($customer->city || $customer->department)
                                    <p class="text-sm text-gray-500">{{ $customer->city }}{{ $customer->city && $customer->department ? ', ' : '' }}{{ $customer->department }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </x-card>

            <!-- Estadísticas -->
            <x-card title="Estadísticas">
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_orders'] }}</p>
                        <p class="text-sm text-gray-500">Órdenes</p>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <p class="text-2xl font-bold text-green-600">${{ number_format($stats['total_spent'], 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-500">Total Compras</p>
                    </div>
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <p class="text-2xl font-bold text-blue-600">${{ number_format($stats['avg_ticket'], 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-500">Ticket Promedio</p>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <p class="text-lg font-bold text-purple-600">{{ $customer->loyalty_points ?? 0 }}</p>
                        <p class="text-sm text-gray-500">Puntos Lealtad</p>
                    </div>
                </div>

                @if($stats['last_order'])
                    <div class="mt-4 pt-4 border-t border-gray-100 text-center">
                        <p class="text-xs text-gray-400">Última compra</p>
                        <p class="text-sm font-medium text-gray-600">{{ $stats['last_order']->format('d/m/Y H:i') }}</p>
                    </div>
                @endif
            </x-card>

            <!-- Notas -->
            @if($customer->notes)
                <x-card title="Notas">
                    <p class="text-gray-600">{{ $customer->notes }}</p>
                </x-card>
            @endif
        </div>

        <!-- Historial de Órdenes -->
        <div class="lg:col-span-2">
            <x-card title="Historial de Órdenes" :padding="false">
                @if($customer->orders->count() > 0)
                    <x-table>
                        <x-slot name="head">
                            <x-th>Orden</x-th>
                            <x-th>Fecha</x-th>
                            <x-th>Estado</x-th>
                            <x-th align="right">Total</x-th>
                        </x-slot>

                        @foreach($customer->orders as $order)
                            <tr class="hover:bg-gray-50">
                                <x-td>
                                    <span class="font-mono font-medium">#{{ $order->order_number }}</span>
                                </x-td>
                                <x-td>
                                    <p class="text-sm">{{ $order->created_at->format('d/m/Y') }}</p>
                                    <p class="text-xs text-gray-400">{{ $order->created_at->format('H:i') }}</p>
                                </x-td>
                                <x-td>
                                    <x-badge :type="match($order->status->value ?? $order->status) {
                                        'paid' => 'success',
                                        'pending' => 'warning',
                                        'cancelled' => 'danger',
                                        default => 'secondary'
                                    }">
                                        {{ match($order->status->value ?? $order->status) {
                                            'paid' => 'Pagada',
                                            'pending' => 'Pendiente',
                                            'cancelled' => 'Cancelada',
                                            default => $order->status
                                        } }}
                                    </x-badge>
                                </x-td>
                                <x-td align="right">
                                    <span class="font-semibold">${{ number_format($order->total, 0, ',', '.') }}</span>
                                </x-td>
                            </tr>
                        @endforeach
                    </x-table>
                @else
                    <div class="p-8 text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-gray-500">Este cliente no tiene órdenes</p>
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</x-layouts.app>
