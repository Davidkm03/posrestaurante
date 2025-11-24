<x-layouts.app title="Órdenes">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Órdenes</h1>
                <p class="text-sm text-gray-500">Historial y gestión de órdenes</p>
            </div>
            <a href="{{ route('pos.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nueva Orden
            </a>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-card class="mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <x-input name="search" placeholder="Buscar por # orden..." :value="request('search')" class="w-48" />
            <x-select name="status" placeholder="Todos los estados" :options="collect($statuses)->mapWithKeys(fn($s) => [$s->value => $s->label()])->toArray()" :selected="request('status')" class="w-40" />
            <x-select name="type" placeholder="Todos los tipos" :options="collect($types)->mapWithKeys(fn($t) => [$t->value => $t->label()])->toArray()" :selected="request('type')" class="w-40" />
            <x-input type="date" name="date_from" :value="request('date_from')" class="w-40" />
            <x-input type="date" name="date_to" :value="request('date_to')" class="w-40" />
            <x-button type="submit" variant="secondary">Filtrar</x-button>
        </form>
    </x-card>

    <!-- Orders Table -->
    <x-card :padding="false">
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

        @if($orders->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $orders->links() }}
            </div>
        @endif
    </x-card>
</x-layouts.app>
