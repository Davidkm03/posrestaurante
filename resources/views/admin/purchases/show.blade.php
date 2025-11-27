<x-layouts.app title="Detalle Orden {{ $purchase->order_number }}">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                    <a href="{{ route('admin.purchases.index') }}" class="hover:text-blue-600">Órdenes de Compra</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span>{{ $purchase->order_number }}</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900">{{ $purchase->order_number }}</h1>
            </div>
            <div class="flex gap-2">
                @if($purchase->canBeReceived())
                    <button onclick="document.getElementById('receiveModal').classList.remove('hidden')" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Recibir Mercancía
                    </button>
                @endif
                @if($purchase->status === 'draft')
                    <a href="{{ route('admin.purchases.edit', $purchase) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Editar
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Info principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Items de la orden -->
            <x-card title="Productos">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ingrediente</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Recibido</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Costo Unit.</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($purchase->items as $item)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <p class="font-medium text-gray-900">{{ $item->ingredient->name ?? 'N/A' }}</p>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-500">
                                        {{ number_format($item->quantity_ordered, 2) }} {{ $item->unit }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right">
                                        @if($item->is_complete)
                                            <span class="text-green-600 font-medium">{{ number_format($item->quantity_received, 2) }}</span>
                                        @else
                                            <span class="text-yellow-600">{{ number_format($item->quantity_received, 2) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-500">
                                        ${{ number_format($item->unit_cost, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-medium text-gray-900">
                                        ${{ number_format($item->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right font-medium text-gray-700">Subtotal:</td>
                                <td class="px-4 py-3 text-right font-bold text-gray-900">${{ number_format($purchase->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @if($purchase->tax_amount > 0)
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right font-medium text-gray-700">IVA:</td>
                                <td class="px-4 py-3 text-right font-bold text-gray-900">${{ number_format($purchase->tax_amount, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right text-lg font-bold text-gray-900">Total:</td>
                                <td class="px-4 py-3 text-right text-lg font-bold text-blue-600">${{ number_format($purchase->total, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </x-card>

            @if($purchase->notes)
            <x-card title="Notas">
                <p class="text-gray-700">{{ $purchase->notes }}</p>
            </x-card>
            @endif
        </div>

        <!-- Info lateral -->
        <div class="space-y-6">
            <!-- Estado -->
            <x-card title="Estado">
                <div class="text-center py-2">
                    <x-badge :type="$purchase->status_color" class="text-lg px-4 py-2">
                        {{ $purchase->status_label }}
                    </x-badge>
                </div>
            </x-card>

            <!-- Proveedor -->
            <x-card title="Proveedor">
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">Nombre</p>
                        <p class="font-medium text-gray-900">{{ $purchase->supplier->name }}</p>
                    </div>
                    @if($purchase->supplier->document_number)
                    <div>
                        <p class="text-sm text-gray-500">NIT/Documento</p>
                        <p class="font-medium text-gray-900">{{ $purchase->supplier->full_document }}</p>
                    </div>
                    @endif
                    @if($purchase->supplier->phone)
                    <div>
                        <p class="text-sm text-gray-500">Teléfono</p>
                        <p class="font-medium text-gray-900">{{ $purchase->supplier->phone }}</p>
                    </div>
                    @endif
                </div>
            </x-card>

            <!-- Fechas -->
            <x-card title="Fechas">
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">Fecha de Orden</p>
                        <p class="font-medium text-gray-900">{{ $purchase->order_date->format('d/m/Y') }}</p>
                    </div>
                    @if($purchase->expected_date)
                    <div>
                        <p class="text-sm text-gray-500">Fecha Esperada</p>
                        <p class="font-medium text-gray-900">{{ $purchase->expected_date->format('d/m/Y') }}</p>
                    </div>
                    @endif
                    @if($purchase->received_date)
                    <div>
                        <p class="text-sm text-gray-500">Fecha Recibido</p>
                        <p class="font-medium text-green-600">{{ $purchase->received_date->format('d/m/Y') }}</p>
                    </div>
                    @endif
                </div>
            </x-card>

            <!-- Creado por -->
            <x-card title="Creado por">
                <p class="font-medium text-gray-900">{{ $purchase->user->name ?? 'N/A' }}</p>
                <p class="text-sm text-gray-500">{{ $purchase->created_at->format('d/m/Y H:i') }}</p>
            </x-card>
        </div>
    </div>

    <!-- Modal de Recepción -->
    @if($purchase->canBeReceived())
    <div id="receiveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Recibir Mercancía</h3>
            </div>
            <form action="{{ route('admin.purchases.receive', $purchase) }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Factura del Proveedor</label>
                        <input type="text" name="supplier_invoice" value="{{ $purchase->supplier_invoice }}"
                            class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0 focus:outline-none"
                            placeholder="Número de factura">
                    </div>
                    
                    <div class="space-y-3">
                        <p class="text-sm font-medium text-gray-700">Cantidades Recibidas:</p>
                        @foreach($purchase->items as $item)
                            <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
                                <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $item->ingredient->name ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-500">
                                        Ordenado: {{ number_format($item->quantity_ordered, 2) }} {{ $item->unit }} | 
                                        Pendiente: {{ number_format($item->pending_quantity, 2) }} {{ $item->unit }}
                                    </p>
                                </div>
                                <div class="w-32">
                                    <input type="number" name="items[{{ $loop->index }}][quantity_received]" 
                                        value="{{ $item->pending_quantity }}" step="0.01" min="0" max="{{ $item->pending_quantity }}"
                                        class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-0 focus:outline-none text-right">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="p-6 border-t flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('receiveModal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Cancelar
                    </button>
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Confirmar Recepción
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</x-layouts.app>
