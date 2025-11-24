<div class="flex flex-col h-full bg-gray-800">
    <!-- Header -->
    <div class="flex-shrink-0 p-3 border-b border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-white">
                    @if($tableId)
                        Mesa {{ $tableId }}
                    @else
                        {{ match($orderType) {
                            'takeaway' => 'Para Llevar',
                            'delivery' => 'Domicilio',
                            default => 'Venta Rápida'
                        } }}
                    @endif
                </h3>
                @if($orderId)
                    <p class="text-xs text-gray-400">Orden activa</p>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <select wire:model.live="guests" class="bg-gray-700 text-white text-sm rounded px-2 py-1 border-gray-600">
                    @for($i = 1; $i <= 20; $i++)
                        <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'persona' : 'personas' }}</option>
                    @endfor
                </select>
                @if(count($items) > 0)
                    <button wire:click="clearCart" wire:confirm="¿Limpiar carrito?" class="p-2 text-gray-400 hover:text-red-400 hover:bg-gray-700 rounded">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Items -->
    <div class="flex-1 overflow-y-auto p-2 space-y-2">
        @forelse($items as $item)
            <div class="bg-gray-700 rounded-lg p-3" wire:key="item-{{ $item['id'] }}">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-white truncate">{{ $item['name'] }}</p>
                        @if(!empty($item['modifiers']))
                            <div class="mt-1">
                                @foreach($item['modifiers'] as $mod)
                                    <span class="text-xs text-blue-400">+ {{ $mod['name'] }}</span>
                                @endforeach
                            </div>
                        @endif
                        @if($item['notes'])
                            <p class="text-xs text-yellow-400 mt-1">{{ $item['notes'] }}</p>
                        @endif
                    </div>
                    <button wire:click="removeItem('{{ $item['id'] }}')" class="ml-2 p-1 text-gray-400 hover:text-red-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="flex items-center justify-between mt-2">
                    <div class="flex items-center gap-2">
                        <button wire:click="decrementQuantity('{{ $item['id'] }}')" class="w-8 h-8 flex items-center justify-center bg-gray-600 hover:bg-gray-500 rounded text-white font-bold">-</button>
                        <span class="w-8 text-center text-white font-medium">{{ $item['quantity'] }}</span>
                        <button wire:click="incrementQuantity('{{ $item['id'] }}')" class="w-8 h-8 flex items-center justify-center bg-gray-600 hover:bg-gray-500 rounded text-white font-bold">+</button>
                    </div>
                    <div class="text-right">
                        <p class="text-white font-semibold">${{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-400">${{ number_format($item['unit_price'], 0, ',', '.') }} c/u</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center h-full text-gray-500">
                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <p>Carrito vacío</p>
                <p class="text-sm">Selecciona productos</p>
            </div>
        @endforelse
    </div>

    <!-- Discount Section -->
    @if(count($items) > 0)
        <div class="flex-shrink-0 p-3 border-t border-gray-700">
            @if($discount > 0)
                <div class="flex items-center justify-between mb-2 p-2 bg-green-900/30 rounded">
                    <span class="text-green-400 text-sm">Descuento aplicado</span>
                    <div class="flex items-center gap-2">
                        <span class="text-green-400 font-medium">-${{ number_format($discount, 0, ',', '.') }}</span>
                        <button wire:click="removeDiscount" class="text-gray-400 hover:text-red-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @else
                <div x-data="{ showDiscount: false }">
                    <button @click="showDiscount = !showDiscount" class="text-sm text-blue-400 hover:text-blue-300">
                        + Agregar descuento
                    </button>
                    <div x-show="showDiscount" x-collapse class="mt-2 space-y-2">
                        <div class="flex gap-2">
                            <select wire:model="discountType" class="bg-gray-700 text-white text-sm rounded px-2 py-1 border-gray-600">
                                <option value="percentage">%</option>
                                <option value="fixed">$</option>
                            </select>
                            <input type="number" wire:model="discountValue" placeholder="Valor" class="flex-1 bg-gray-700 text-white text-sm rounded px-2 py-1 border-gray-600">
                        </div>
                        <input type="text" wire:model="discountReason" placeholder="Motivo (opcional)" class="w-full bg-gray-700 text-white text-sm rounded px-2 py-1 border-gray-600">
                        <button wire:click="applyDiscount" class="w-full py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">Aplicar</button>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Totals -->
    <div class="flex-shrink-0 p-3 border-t border-gray-700 space-y-2">
        <div class="flex justify-between text-sm text-gray-400">
            <span>Subtotal</span>
            <span>${{ number_format($subtotal, 0, ',', '.') }}</span>
        </div>
        @if($discount > 0)
            <div class="flex justify-between text-sm text-green-400">
                <span>Descuento</span>
                <span>-${{ number_format($discount, 0, ',', '.') }}</span>
            </div>
        @endif
        <div class="flex justify-between text-sm text-gray-400">
            <span>IVA</span>
            <span>${{ number_format($tax, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between text-xl font-bold text-white pt-2 border-t border-gray-600">
            <span>Total</span>
            <span>${{ number_format($total, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex-shrink-0 p-3 border-t border-gray-700 space-y-2">
        <div class="grid grid-cols-2 gap-2">
            <button wire:click="sendToKitchen" @disabled(count($items) === 0) class="py-3 bg-orange-600 hover:bg-orange-700 disabled:bg-gray-600 disabled:cursor-not-allowed text-white font-semibold rounded-lg transition-colors">
                <span class="flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                    </svg>
                    Cocina
                </span>
            </button>
            <button wire:click="goToPayment" @disabled(count($items) === 0) class="py-3 bg-green-600 hover:bg-green-700 disabled:bg-gray-600 disabled:cursor-not-allowed text-white font-semibold rounded-lg transition-colors">
                <span class="flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Cobrar
                </span>
            </button>
        </div>
    </div>
</div>
