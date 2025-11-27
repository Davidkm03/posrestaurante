<x-layouts.app title="Admin">
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.credit-notes.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Nota de Crédito {{ $creditNote->number ?? '#' . $creditNote->id }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <!-- Header -->
                <div class="p-6 border-b bg-gradient-to-r from-red-50 to-orange-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center">
                                <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">{{ $creditNote->number ?? 'NC-' . str_pad($creditNote->id, 6, '0', STR_PAD_LEFT) }}</h3>
                                <p class="text-sm text-gray-500">{{ $creditNote->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <span class="px-4 py-2 rounded-full text-sm font-medium
                            {{ $creditNote->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $creditNote->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $creditNote->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ $creditNote->status === 'approved' ? '✓ Aprobada' : ($creditNote->status === 'pending' ? '⏳ Pendiente' : '✗ Rechazada') }}
                        </span>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Información de referencia -->
                    <div class="grid grid-cols-2 gap-6">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Factura de Referencia</h4>
                            @if($creditNote->invoice)
                                <a href="{{ route('admin.invoices.show', $creditNote->invoice_id) }}" 
                                   class="text-blue-600 hover:underline font-medium">
                                    {{ $creditNote->invoice->invoice_number }}
                                </a>
                                <p class="text-sm text-gray-500 mt-1">
                                    Total original: ${{ number_format($creditNote->invoice->total, 0) }}
                                </p>
                            @else
                                <span class="text-gray-400">N/A</span>
                            @endif
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Cliente</h4>
                            @if($creditNote->customer)
                                <p class="font-medium text-gray-900">
                                    {{ $creditNote->customer->first_name }} {{ $creditNote->customer->last_name }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ $creditNote->customer->document_type }}: {{ $creditNote->customer->document_number }}
                                </p>
                            @else
                                <span class="text-gray-400">Consumidor Final</span>
                            @endif
                        </div>
                    </div>

                    <!-- Tipo de anulación -->
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500">Tipo:</span>
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $creditNote->type === 'full' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $creditNote->type === 'full' ? 'Anulación Total' : 'Anulación Parcial' }}
                        </span>
                    </div>

                    <!-- Motivo -->
                    <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                        <h4 class="text-sm font-medium text-red-800 mb-1">Motivo de la Anulación</h4>
                        <p class="text-gray-700">{{ $creditNote->reason }}</p>
                    </div>

                    <!-- Items -->
                    @if($creditNote->items && count($creditNote->items) > 0)
                        <div>
                            <h4 class="font-medium text-gray-900 mb-3">Items Anulados</h4>
                            <div class="border rounded-lg overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Valor Unit.</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($creditNote->items as $item)
                                            <tr>
                                                <td class="px-4 py-3 text-gray-900">{{ $item->description }}</td>
                                                <td class="px-4 py-3 text-center text-gray-500">{{ $item->quantity }}</td>
                                                <td class="px-4 py-3 text-right text-gray-500">${{ number_format($item->unit_price, 0) }}</td>
                                                <td class="px-4 py-3 text-right font-medium text-red-600">-${{ number_format($item->quantity * $item->unit_price, 0) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Totales -->
                    <div class="flex justify-end">
                        <div class="w-72 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Subtotal:</span>
                                <span class="font-medium">${{ number_format($creditNote->subtotal, 0) }}</span>
                            </div>
                            @if($creditNote->tax > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">IVA:</span>
                                    <span class="font-medium">${{ number_format($creditNote->tax, 0) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-lg font-bold border-t pt-2">
                                <span class="text-gray-900">Total Devolución:</span>
                                <span class="text-red-600">-${{ number_format($creditNote->total, 0) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- DIAN Info -->
                    @if($creditNote->dian_uuid)
                        <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                            <h4 class="text-sm font-medium text-green-800 mb-2">Información DIAN</h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">CUDE:</span>
                                    <p class="font-mono text-xs text-gray-700 break-all">{{ $creditNote->dian_uuid }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500">Validado:</span>
                                    <p class="text-gray-700">{{ $creditNote->dian_validated_at?->format('d/m/Y H:i') ?? 'Pendiente' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Footer con acciones -->
                <div class="px-6 py-4 bg-gray-50 border-t flex justify-between">
                    <a href="{{ route('admin.credit-notes.index') }}" 
                       class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100">
                        ← Volver al listado
                    </a>
                    <div class="flex gap-2">
                        @if($creditNote->dian_uuid)
                            <a href="#" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Descargar PDF
                            </a>
                        @endif
                        <button onclick="window.print()" 
                                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
