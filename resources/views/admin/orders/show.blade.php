<x-layouts.app title="Orden {{ $order->order_number }}">
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2 lg:gap-4 min-w-0 flex-1">
                <a href="{{ route('admin.orders.index') }}" class="p-1.5 lg:p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg flex-shrink-0">
                    <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div class="min-w-0 flex-1">
                    <h1 class="text-lg lg:text-2xl font-bold text-gray-900 font-mono truncate">{{ $order->order_number }}</h1>
                    <p class="text-xs lg:text-sm text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 lg:gap-3 flex-shrink-0">
                <x-badge :type="$order->status_color" size="lg" class="hidden sm:inline-flex">{{ $order->status_label }}</x-badge>
                <x-badge :type="$order->status_color" class="sm:hidden">{{ Str::limit($order->status_label, 8) }}</x-badge>
                <a href="{{ route('admin.orders.print', $order) }}" target="_blank" class="p-1.5 lg:p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
        <!-- Order Details -->
        <div class="lg:col-span-2 space-y-4 lg:space-y-6">
            <!-- Items -->
            <x-card title="Items de la Orden" :padding="false">
                <div class="divide-y divide-gray-200">
                    @foreach($order->items as $item)
                        <div class="p-3 lg:p-4 flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-blue-600 text-sm lg:text-base">{{ intval($item->quantity) }}x</span>
                                    <span class="font-medium text-gray-900 text-sm lg:text-base truncate">{{ $item->name }}</span>
                                </div>
                                @if($item->modifiers->isNotEmpty())
                                    <div class="mt-1 ml-6 lg:ml-8">
                                        @foreach($item->modifiers as $mod)
                                            <p class="text-xs lg:text-sm text-gray-500">+ {{ $mod->name }}</p>
                                        @endforeach
                                    </div>
                                @endif
                                @if($item->notes)
                                    <p class="mt-1 ml-6 lg:ml-8 text-xs lg:text-sm text-orange-600 italic">{{ $item->notes }}</p>
                                @endif
                            </div>
                            <div class="text-right ml-2 flex-shrink-0">
                                <p class="font-semibold text-sm lg:text-base">${{ number_format($item->total, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500">${{ number_format($item->unit_price, 0, ',', '.') }} c/u</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Totals -->
                <div class="border-t border-gray-200 p-3 lg:p-4 bg-gray-50">
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium">${{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @if($order->discount_amount > 0)
                            <div class="flex justify-between text-sm text-red-600">
                                <span>Descuento</span>
                                <span class="font-medium">-${{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Impuestos</span>
                            <span class="font-medium">${{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                        </div>
                        @if($order->tip_amount > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Propina</span>
                                <span class="font-medium">${{ number_format($order->tip_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-base lg:text-lg font-bold pt-2 border-t border-gray-300">
                            <span>Total</span>
                            <span>${{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Payments -->
            @if($order->payments->isNotEmpty())
                <x-card title="Pagos">
                    <div class="space-y-3">
                        @foreach($order->payments as $payment)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium">{{ $payment->paymentMethod->name ?? 'Efectivo' }}</p>
                                        <p class="text-xs text-gray-500">{{ $payment->processed_at?->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                                <span class="font-semibold text-green-600">${{ number_format($payment->amount, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            @endif
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-4 lg:space-y-6">
            <!-- Order Info -->
            <x-card title="Información">
                <dl class="space-y-3 lg:space-y-4 text-sm lg:text-base">
                    <div>
                        <dt class="text-xs lg:text-sm text-gray-500">Tipo de orden</dt>
                        <dd class="font-medium mt-1">{{ $order->type_label }}</dd>
                    </div>
                    @if($order->table)
                        <div>
                            <dt class="text-xs lg:text-sm text-gray-500">Mesa</dt>
                            <dd class="font-medium mt-1">{{ $order->table->number }} ({{ $order->table->zone->name ?? '' }})</dd>
                        </div>
                    @endif
                    @if($order->guests)
                        <div>
                            <dt class="text-xs lg:text-sm text-gray-500">Comensales</dt>
                            <dd class="font-medium mt-1">{{ $order->guests }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-xs lg:text-sm text-gray-500">Mesero</dt>
                        <dd class="font-medium mt-1">{{ $order->waiter->name ?? 'No asignado' }}</dd>
                    </div>
                    @if($order->cashier)
                        <div>
                            <dt class="text-xs lg:text-sm text-gray-500">Cajero</dt>
                            <dd class="font-medium mt-1">{{ $order->cashier->name }}</dd>
                        </div>
                    @endif
                </dl>
            </x-card>

            <!-- Customer -->
            @if($order->customer)
                <x-card title="Cliente">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
                            <span class="text-lg font-medium text-gray-600">{{ substr($order->customer->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-medium">{{ $order->customer->name }}</p>
                            <p class="text-sm text-gray-500">{{ $order->customer->document_number }}</p>
                        </div>
                    </div>
                </x-card>
            @endif

            <!-- Timeline -->
            <x-card title="Historial">
                <div class="space-y-4">
                    <div class="flex gap-3">
                        <div class="w-2 h-2 mt-2 bg-green-500 rounded-full"></div>
                        <div>
                            <p class="text-sm font-medium">Orden creada</p>
                            <p class="text-xs text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @if($order->completed_at)
                        <div class="flex gap-3">
                            <div class="w-2 h-2 mt-2 bg-green-500 rounded-full"></div>
                            <div>
                                <p class="text-sm font-medium">Orden completada</p>
                                <p class="text-xs text-gray-500">{{ $order->completed_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </x-card>

            <!-- Actions -->
            @if($order->canBeCancelled())
                <x-card>
                    <button
                        type="button"
                        onclick="document.getElementById('cancel-modal').classList.remove('hidden')"
                        class="w-full px-4 py-2 text-red-600 border border-red-300 rounded-lg hover:bg-red-50"
                    >
                        Cancelar Orden
                    </button>
                </x-card>
            @endif
        </div>
    </div>

    <!-- Cancel Modal -->
    <div id="cancel-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" onclick="document.getElementById('cancel-modal').classList.add('hidden')"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Cancelar Orden</h3>
                <form action="{{ route('admin.orders.cancel', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <x-textarea name="reason" label="Motivo de cancelación" required rows="3" placeholder="Ingresa el motivo..." />
                    <div class="mt-4 flex gap-3">
                        <button type="button" onclick="document.getElementById('cancel-modal').classList.add('hidden')" class="flex-1 px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            No, volver
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700">
                            Sí, cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
