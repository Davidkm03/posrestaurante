<x-layouts.app title="Órdenes">
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div class="min-w-0 flex-1">
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900 truncate">Órdenes</h1>
                <p class="text-xs lg:text-sm text-gray-500 mt-1">Historial y gestión de órdenes</p>
            </div>
            <a href="{{ route('pos.index') }}" class="inline-flex items-center gap-2 px-3 lg:px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm lg:text-base flex-shrink-0">
                <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span class="hidden sm:inline">Nueva Orden</span>
                <span class="sm:hidden">Nueva</span>
            </a>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-card class="mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div class="md:col-span-2">
                <x-input 
                    name="search" 
                    placeholder="Buscar por # orden..." 
                    :value="request('search')"
                />
            </div>
            <div>
                <x-select 
                    name="status" 
                    placeholder="Todos los estados" 
                    :options="collect($statuses)->mapWithKeys(fn($s) => [$s->value => $s->label()])->toArray()" 
                    :selected="request('status')"
                />
            </div>
            <div>
                <x-select 
                    name="type" 
                    placeholder="Todos los tipos" 
                    :options="collect($types)->mapWithKeys(fn($t) => [$t->value => $t->label()])->toArray()" 
                    :selected="request('type')"
                />
            </div>
            <div>
                <x-input 
                    type="date" 
                    name="date_from" 
                    placeholder="Desde"
                    :value="request('date_from')"
                />
            </div>
            <div>
                <x-input 
                    type="date" 
                    name="date_to" 
                    placeholder="Hasta"
                    :value="request('date_to')"
                />
            </div>
            <div class="md:col-span-6 flex gap-2">
                <x-button type="submit" variant="primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Filtrar
                </x-button>
                @if(request()->hasAny(['search', 'status', 'type', 'date_from', 'date_to']))
                    <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Limpiar
                    </a>
                @endif
            </div>
        </form>
    </x-card>

    <!-- Orders Table -->
    <x-card :padding="false">
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <x-table>
                <x-slot name="head">
                    <x-th># Orden</x-th>
                    <x-th>Tipo</x-th>
                    <x-th>Mesa/Cliente</x-th>
                    <x-th>Mesero</x-th>
                    <x-th align="right">Total</x-th>
                    <x-th align="center">Estado</x-th>
                    <x-th>Fecha</x-th>
                    <x-th align="right">Acciones</x-th>
                </x-slot>

                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <x-td>
                            <a href="{{ route('admin.orders.show', $order) }}" class="font-mono font-medium text-blue-600 hover:text-blue-800">
                                {{ $order->order_number }}
                            </a>
                        </x-td>
                        <x-td>
                            <x-badge :type="match($order->type) {
                                'dine_in' => 'primary',
                                'takeaway' => 'warning',
                                'delivery' => 'purple',
                                default => 'secondary'
                            }">
                                {{ $order->type_label }}
                            </x-badge>
                        </x-td>
                        <x-td>
                            @if($order->table)
                                <span class="font-medium">Mesa {{ $order->table->number }}</span>
                            @elseif($order->customer)
                                {{ $order->customer->name }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </x-td>
                        <x-td>{{ $order->waiter->name ?? '-' }}</x-td>
                        <x-td align="right">
                            <span class="font-semibold">${{ number_format($order->total, 0, ',', '.') }}</span>
                        </x-td>
                        <x-td align="center">
                            <x-badge :type="$order->status_color" dot>
                                {{ $order->status_label }}
                            </x-badge>
                        </x-td>
                        <x-td>
                            <div>
                                <p class="text-sm">{{ $order->created_at->format('d/m/Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $order->created_at->format('H:i') }}</p>
                            </div>
                        </x-td>
                        <x-td align="right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.orders.show', $order) }}" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg" title="Ver detalles">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.orders.print', $order) }}" target="_blank" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg" title="Imprimir">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                </a>
                            </div>
                        </x-td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <x-empty-state title="No hay órdenes" description="Las órdenes aparecerán aquí" />
                        </td>
                    </tr>
                @endforelse
            </x-table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden divide-y divide-gray-200">
            @forelse($orders as $order)
                <div class="p-4 hover:bg-gray-50">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <a href="{{ route('admin.orders.show', $order) }}" class="font-mono text-lg font-semibold text-blue-600">
                                {{ $order->order_number }}
                            </a>
                            <p class="text-sm text-gray-500 mt-1">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <x-badge :type="$order->status_color" dot>
                            {{ $order->status_label }}
                        </x-badge>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Tipo</p>
                            <x-badge :type="match($order->type) {
                                'dine_in' => 'primary',
                                'takeaway' => 'warning',
                                'delivery' => 'purple',
                                default => 'secondary'
                            }" size="sm">
                                {{ $order->type_label }}
                            </x-badge>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Mesa/Cliente</p>
                            <p class="text-sm font-medium">
                                @if($order->table)
                                    Mesa {{ $order->table->number }}
                                @elseif($order->customer)
                                    {{ Str::limit($order->customer->name, 15) }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Mesero</p>
                            <p class="text-sm">{{ $order->waiter->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Total</p>
                            <p class="text-lg font-bold text-gray-900">${{ number_format($order->total, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-3 border-t border-gray-200">
                        <a href="{{ route('admin.orders.show', $order) }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Ver
                        </a>
                        <a href="{{ route('admin.orders.print', $order) }}" target="_blank" class="inline-flex items-center justify-center p-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-8">
                    <x-empty-state title="No hay órdenes" description="Las órdenes aparecerán aquí" />
                </div>
            @endforelse
        </div>

        @if($orders->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $orders->links() }}
            </div>
        @endif
    </x-card>
</x-layouts.app>
