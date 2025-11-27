<div>
    @if($isOpen && $order)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80" wire:click.self="close">
        <div class="bg-gray-800 rounded-xl shadow-2xl w-full max-w-4xl mx-4 max-h-[95vh] overflow-hidden flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-700">
            <div>
                <h2 class="text-xl font-bold text-white">Procesar Pago</h2>
                <p class="text-gray-400">Orden: {{ $order->order_number }}</p>
            </div>
            <button wire:click="close" class="p-2 text-gray-400 hover:text-white rounded-lg hover:bg-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Body -->
        <div class="flex-1 overflow-y-auto p-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column: Order Summary & Customer -->
                <div class="space-y-4">
                    <!-- Order Summary -->
                    <div class="bg-gray-700/50 rounded-lg p-4">
                        <h3 class="font-semibold text-white mb-3">Resumen de Orden</h3>
                        <div class="space-y-2 text-sm max-h-40 overflow-y-auto">
                            @foreach($order->items as $item)
                                <div class="flex justify-between text-gray-300">
                                    <span>{{ intval($item->quantity) }}x {{ $item->name }}</span>
                                    <span>${{ number_format($item->total, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="border-t border-gray-600 mt-3 pt-3 space-y-1">
                            @if($order->discount_amount > 0)
                                <div class="flex justify-between text-gray-400 text-sm">
                                    <span>Subtotal</span>
                                    <span>${{ number_format($order->subtotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-green-400 text-sm">
                                    <span>Descuento</span>
                                    <span>-${{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-white text-xl font-bold {{ $order->discount_amount > 0 ? 'pt-2' : '' }}">
                                <span>TOTAL</span>
                                <span>${{ number_format($order->total, 0, ',', '.') }}</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">* Precio incluye IVA</p>
                        </div>
                    </div>

                    <!-- Customer Section -->
                    <div class="bg-gray-700/50 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold text-white">Cliente</h3>
                            @if(!$showCustomerSearch)
                                <button wire:click="$set('showCustomerSearch', true)" class="text-blue-400 hover:text-blue-300 text-sm">
                                    {{ $customer ? 'Cambiar' : '+ Agregar' }}
                                </button>
                            @endif
                        </div>

                        @if($showCustomerSearch)
                            <div class="space-y-2">
                                <input type="text"
                                       wire:model.live.debounce.300ms="customerSearch"
                                       wire:keyup="searchCustomer"
                                       placeholder="Buscar por nombre, documento o teléfono..."
                                       class="w-full bg-gray-700 text-white rounded-lg border border-gray-600 p-2 text-sm">

                                @if(!empty($customerResults))
                                    <div class="bg-gray-700 rounded-lg divide-y divide-gray-600">
                                        @foreach($customerResults as $result)
                                            <button wire:click="selectCustomer({{ $result['id'] }})"
                                                    class="w-full p-2 text-left hover:bg-gray-600 first:rounded-t-lg last:rounded-b-lg">
                                                <p class="text-white font-medium">{{ $result['name'] }}</p>
                                                <p class="text-gray-400 text-xs">{{ $result['document_number'] ?? 'Sin documento' }}</p>
                                            </button>
                                        @endforeach
                                    </div>
                                @elseif(strlen($customerSearch) >= 2)
                                    <div class="bg-gray-800 rounded-lg p-3 text-center">
                                        <p class="text-gray-400 text-sm mb-2">Cliente no encontrado</p>
                                        <button wire:click="showCreateForm"
                                                class="text-blue-400 hover:text-blue-300 text-sm font-medium">
                                            + Crear nuevo cliente
                                        </button>
                                    </div>
                                @endif

                                <button wire:click="$set('showCustomerSearch', false)" class="text-gray-400 hover:text-white text-sm">
                                    Cancelar
                                </button>
                            </div>
                        @elseif($showCustomerCreate)
                            <!-- Create Customer Form -->
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-gray-400 text-xs mb-1">Nombre *</label>
                                    <input type="text"
                                           wire:model="newCustomer.name"
                                           placeholder="Nombre del cliente"
                                           class="w-full px-3 py-2 bg-gray-700 text-white rounded-lg border border-gray-600 text-sm">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-gray-400 text-xs mb-1">Tipo Doc.</label>
                                        <select wire:model="newCustomer.document_type"
                                                class="w-full px-3 py-2 bg-gray-700 text-white rounded-lg border border-gray-600 text-sm">
                                            <option value="13">CC - Cédula de Ciudadanía</option>
                                            <option value="31">NIT - Número de Identificación Tributaria</option>
                                            <option value="22">CE - Cédula de Extranjería</option>
                                            <option value="41">Pasaporte</option>
                                            <option value="12">TI - Tarjeta de Identidad</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-gray-400 text-xs mb-1">Documento *</label>
                                        <input type="text"
                                               wire:model="newCustomer.document_number"
                                               placeholder="Número"
                                               class="w-full px-3 py-2 bg-gray-700 text-white rounded-lg border border-gray-600 text-sm">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-gray-400 text-xs mb-1">Teléfono</label>
                                        <input type="tel"
                                               wire:model="newCustomer.phone"
                                               placeholder="Teléfono"
                                               class="w-full px-3 py-2 bg-gray-700 text-white rounded-lg border border-gray-600 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-gray-400 text-xs mb-1">Email</label>
                                        <input type="email"
                                               wire:model="newCustomer.email"
                                               placeholder="Email"
                                               class="w-full px-3 py-2 bg-gray-700 text-white rounded-lg border border-gray-600 text-sm">
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button wire:click="createCustomer"
                                            class="flex-1 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium">
                                        Crear
                                    </button>
                                    <button wire:click="cancelCreateCustomer"
                                            class="flex-1 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg text-sm">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        @elseif($customer)
                            <div class="flex items-center justify-between bg-gray-700 rounded-lg p-3">
                                <div>
                                    <p class="text-white font-medium">{{ $customer->name }}</p>
                                    <p class="text-gray-400 text-sm">{{ $customer->document_type->shortLabel() }}: {{ $customer->document_number }}</p>
                                </div>
                                <button wire:click="removeCustomer" class="text-red-400 hover:text-red-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        @else
                            <p class="text-gray-400 text-sm">Consumidor Final</p>
                        @endif
                    </div>

                    <!-- Invoice Type -->
                    <div class="bg-gray-700/50 rounded-lg p-4">
                        <h3 class="font-semibold text-white mb-3">Tipo de Documento</h3>
                        <div class="grid grid-cols-2 gap-2">
                            <button wire:click="$set('invoiceType', 'pos')"
                                    class="p-3 rounded-lg text-center transition-colors {{ $invoiceType === 'pos' ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }}">
                                <svg class="w-6 h-6 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="text-sm">Tiquete POS</span>
                            </button>
                            <button wire:click="$set('invoiceType', 'electronic')"
                                    class="p-3 rounded-lg text-center transition-colors {{ $invoiceType === 'electronic' ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }}">
                                <svg class="w-6 h-6 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm">Factura Electrónica</span>
                            </button>
                        </div>
                        @if($invoiceType === 'electronic' && !$customer)
                            <p class="text-yellow-400 text-xs mt-2">⚠ Se requiere cliente para factura electrónica</p>
                        @endif
                    </div>
                </div>

                <!-- Right Column: Payment Methods -->
                <div class="space-y-4">
                    <!-- Payment Methods -->
                    <div class="bg-gray-700/50 rounded-lg p-4">
                        <h3 class="font-semibold text-white mb-3">Método de Pago</h3>
                        <div class="grid grid-cols-3 gap-2 mb-4">
                            @foreach(['cash' => 'Efectivo', 'card' => 'Tarjeta', 'transfer' => 'Transferencia', 'nequi' => 'Nequi', 'daviplata' => 'Daviplata', 'other' => 'Otro'] as $method => $label)
                                <button wire:click="selectPaymentMethod('{{ $method }}')"
                                        class="p-2 rounded-lg text-center transition-colors text-sm {{ $selectedMethod === $method ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>

                        <!-- Amount Input -->
                        <div class="space-y-3">
                            <div>
                                <label class="block text-gray-400 text-sm mb-1">Monto</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-gray-400">$</span>
                                    <input type="number"
                                           wire:model="paymentAmount"
                                           class="w-full pl-8 pr-4 py-2 bg-gray-700 text-white text-lg font-bold rounded-lg border border-gray-600 focus:border-blue-500">
                                </div>
                            </div>

                            @if($selectedMethod === 'cash')
                                <!-- Quick Amounts -->
                                <div class="grid grid-cols-4 gap-2">
                                    @foreach($quickAmounts as $amount)
                                        <button wire:click="setQuickAmount({{ $amount }})"
                                                class="p-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg text-sm">
                                            ${{ number_format($amount, 0, ',', '.') }}
                                        </button>
                                    @endforeach
                                    <button wire:click="setExactAmount"
                                            class="p-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm">
                                        Exacto
                                    </button>
                                </div>
                            @else
                                <!-- Reference -->
                                <div>
                                    <label class="block text-gray-400 text-sm mb-1">Referencia / Aprobación</label>
                                    <input type="text"
                                           wire:model="reference"
                                           placeholder="Número de referencia..."
                                           class="w-full px-3 py-2 bg-gray-700 text-white rounded-lg border border-gray-600">
                                </div>
                            @endif

                            <button wire:click="addPayment"
                                    class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg">
                                Agregar Pago
                            </button>
                        </div>
                    </div>

                    <!-- Added Payments -->
                    @if(!empty($payments))
                        <div class="bg-gray-700/50 rounded-lg p-4">
                            <h3 class="font-semibold text-white mb-3">Pagos Agregados</h3>
                            <div class="space-y-2">
                                @foreach($payments as $index => $payment)
                                    <div class="flex items-center justify-between bg-gray-700 rounded-lg p-2">
                                        <div>
                                            <span class="text-white capitalize">{{ $payment['method'] }}</span>
                                            @if($payment['reference'])
                                                <span class="text-gray-400 text-sm ml-2">Ref: {{ $payment['reference'] }}</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-green-400 font-bold">${{ number_format($payment['amount'], 0, ',', '.') }}</span>
                                            <button wire:click="removePayment({{ $index }})" class="text-red-400 hover:text-red-300">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Payment Summary -->
                    <div class="bg-gray-700/50 rounded-lg p-4">
                        <div class="space-y-2">
                            <div class="flex justify-between text-gray-300">
                                <span>Total a Pagar</span>
                                <span class="font-bold">${{ number_format($order->total, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-gray-300">
                                <span>Total Pagado</span>
                                <span class="font-bold text-green-400">${{ number_format($totalPaid, 0, ',', '.') }}</span>
                            </div>
                            @if($remaining > 0)
                                <div class="flex justify-between text-yellow-400">
                                    <span>Pendiente</span>
                                    <span class="font-bold">${{ number_format($remaining, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($change > 0)
                                <div class="flex justify-between text-xl text-white bg-green-600 rounded-lg p-2 -mx-2">
                                    <span>Cambio</span>
                                    <span class="font-bold">${{ number_format($change, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex-shrink-0 p-4 border-t border-gray-700">
            <button wire:click="processPayment"
                    @disabled($remaining > 0 || empty($payments))
                    class="w-full py-4 bg-green-600 hover:bg-green-700 disabled:bg-gray-600 disabled:cursor-not-allowed text-white text-xl font-bold rounded-lg transition-colors">
                @if($remaining > 0)
                    Falta ${{ number_format($remaining, 0, ',', '.') }}
                @else
                    Completar Pago
                @endif
            </button>
        </div>
    </div>
    @endif
</div>
