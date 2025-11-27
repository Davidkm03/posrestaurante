<x-layouts.app title="Factura {{ $invoice->getFullNumber() }}">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Factura {{ $invoice->getFullNumber() }}</h1>
                <p class="text-sm text-gray-500">Detalle de la factura electrónica</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.invoices.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    ← Volver
                </a>
                <a href="{{ route('admin.invoices.print', $invoice) }}" target="_blank" class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Imprimir
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información Principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Datos de la Factura -->
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Información de la Factura</h2>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full 
                        @if($invoice->status->value === 'approved') bg-green-100 text-green-800
                        @elseif($invoice->status->value === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($invoice->status->value === 'rejected') bg-red-100 text-red-800
                        @elseif($invoice->status->value === 'voided') bg-gray-100 text-gray-800
                        @else bg-blue-100 text-blue-800
                        @endif">
                        {{ $invoice->status->label() }}
                    </span>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Número</p>
                        <p class="font-semibold text-gray-900">{{ $invoice->getFullNumber() }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Fecha Emisión</p>
                        <p class="font-semibold text-gray-900">{{ $invoice->issue_date?->format('d/m/Y') ?? $invoice->issue_date }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Hora</p>
                        <p class="font-semibold text-gray-900">{{ $invoice->issue_time }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Vencimiento</p>
                        <p class="font-semibold text-gray-900">{{ $invoice->due_date?->format('d/m/Y') ?? $invoice->due_date }}</p>
                    </div>
                </div>

                @if($invoice->cufe || $invoice->dian_cufe)
                    <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-semibold text-green-800">Documento Electrónico Validado</span>
                            @if($invoice->dian_status === 'SIMULADO')
                                <span class="ml-2 px-2 py-0.5 text-xs bg-yellow-100 text-yellow-800 rounded">SIMULADO</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mb-1">CUFE</p>
                        <p class="text-xs font-mono text-gray-700 break-all">{{ $invoice->dian_cufe ?? $invoice->cufe }}</p>
                        @if($invoice->dian_uuid)
                            <p class="text-xs text-gray-500 mt-2 mb-1">UUID</p>
                            <p class="text-xs font-mono text-gray-700">{{ $invoice->dian_uuid }}</p>
                        @endif
                    </div>
                @endif
            </x-card>

            <!-- Cliente -->
            <x-card>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Cliente</h2>
                @if($invoice->customer)
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Nombre</p>
                            <p class="font-semibold text-gray-900">{{ $invoice->customer->full_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Documento</p>
                            <p class="font-semibold text-gray-900">{{ $invoice->customer->document_type }} {{ $invoice->customer->document_number }}</p>
                        </div>
                        @if($invoice->customer->email)
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="font-semibold text-gray-900">{{ $invoice->customer->email }}</p>
                            </div>
                        @endif
                        @if($invoice->customer->phone)
                            <div>
                                <p class="text-sm text-gray-500">Teléfono</p>
                                <p class="font-semibold text-gray-900">{{ $invoice->customer->phone }}</p>
                            </div>
                        @endif
                        @if($invoice->customer->address)
                            <div class="col-span-2">
                                <p class="text-sm text-gray-500">Dirección</p>
                                <p class="font-semibold text-gray-900">{{ $invoice->customer->address }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-gray-500">Sin cliente asignado</p>
                @endif
            </x-card>

            <!-- Items de la Factura -->
            <x-card :padding="false">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Detalle de Productos</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Cant.</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Precio Unit.</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">IVA</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($invoice->lines as $line)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $line->description }}</div>
                                        @if($line->code)
                                            <div class="text-xs text-gray-500">Código: {{ $line->code }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-900">{{ number_format($line->quantity, 0) }}</td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-900">${{ number_format($line->unit_price, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-500">
                                        @if($line->tax_percentage > 0)
                                            {{ number_format($line->tax_percentage, 0) }}%
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-medium text-gray-900">${{ number_format($line->line_total, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                @if($invoice->order && $invoice->order->items)
                                    @foreach($invoice->order->items as $item)
                                        <tr>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $item->product_name }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-center text-sm text-gray-900">{{ number_format($item->quantity, 0) }}</td>
                                            <td class="px-6 py-4 text-right text-sm text-gray-900">${{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                            <td class="px-6 py-4 text-right text-sm text-gray-500">
                                                @if($item->tax_percentage > 0)
                                                    {{ number_format($item->tax_percentage, 0) }}%
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-right text-sm font-medium text-gray-900">${{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                            No hay items en esta factura
                                        </td>
                                    </tr>
                                @endif
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Totales -->
            <x-card>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Resumen</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">${{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if($invoice->total_discount > 0)
                        <div class="flex justify-between text-red-600">
                            <span>Descuento</span>
                            <span>-${{ number_format($invoice->total_discount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if($invoice->total_tax_iva > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-600">IVA</span>
                            <span class="font-medium">${{ number_format($invoice->total_tax_iva, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if($invoice->total_tax_inc > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Impoconsumo</span>
                            <span class="font-medium">${{ number_format($invoice->total_tax_inc, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="border-t pt-3 flex justify-between">
                        <span class="text-lg font-bold text-gray-900">Total</span>
                        <span class="text-lg font-bold text-gray-900">${{ number_format($invoice->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </x-card>

            <!-- Pagos -->
            <x-card>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Pagos</h2>
                @if($invoice->payments && $invoice->payments->count() > 0)
                    <div class="space-y-3">
                        @foreach($invoice->payments as $payment)
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $payment->paymentMethod->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ $payment->paid_at?->format('d/m/Y H:i') ?? $payment->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <span class="font-semibold text-green-600">${{ number_format($payment->amount, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No hay pagos registrados</p>
                @endif
            </x-card>

            <!-- Orden Relacionada -->
            @if($invoice->order)
                <x-card>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Orden Relacionada</h2>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Número</span>
                            <span class="font-medium">#{{ $invoice->order->id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Estado</span>
                            <span class="font-medium">{{ $invoice->order->status->label() ?? ucfirst($invoice->order->status->value ?? $invoice->order->status) }}</span>
                        </div>
                        @if($invoice->order->user)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Atendido por</span>
                                <span class="font-medium">{{ $invoice->order->user->name }}</span>
                            </div>
                        @endif
                    </div>
                </x-card>
            @endif

            <!-- Acciones -->
            <x-card>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Acciones</h2>
                <div class="space-y-2">
                    <a href="{{ route('admin.invoices.print', $invoice) }}" target="_blank" 
                       class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Imprimir
                    </a>
                    
                    @if($invoice->canBeVoided())
                        <form action="{{ route('admin.invoices.cancel', $invoice) }}" method="POST" 
                              onsubmit="return confirm('¿Estás seguro de anular esta factura?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                </svg>
                                Anular Factura
                            </button>
                        </form>
                    @endif
                </div>
            </x-card>

            <!-- Notas -->
            @if($invoice->notes)
                <x-card>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Notas</h2>
                    <p class="text-gray-600">{{ $invoice->notes }}</p>
                </x-card>
            @endif
        </div>
    </div>
</x-layouts.app>
