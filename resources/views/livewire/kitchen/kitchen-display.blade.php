<div class="h-screen flex flex-col bg-gray-900" wire:poll.{{ $refreshInterval }}s>
    <!-- Header -->
    <div class="flex-shrink-0 bg-gray-800 border-b border-gray-700">
        <div class="flex items-center justify-between p-4">
            <div class="flex items-center gap-4">
                <h1 class="text-2xl font-bold text-white">Kitchen Display</h1>
                <div class="flex items-center gap-2 text-sm">
                    <span class="px-2 py-1 bg-yellow-600 rounded text-white">{{ $pendingOrders->count() }} Pendientes</span>
                    <span class="px-2 py-1 bg-blue-600 rounded text-white">{{ $preparingOrders->count() }} Preparando</span>
                    <span class="px-2 py-1 bg-green-600 rounded text-white">{{ $readyOrders->count() }} Listas</span>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <!-- Station Filter -->
                <div class="flex gap-1">
                    @foreach($stations as $key => $label)
                        <button wire:click="selectStation('{{ $key }}')"
                                class="px-3 py-1 rounded text-sm font-medium transition-colors {{ $selectedStation === $key ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                <!-- Toggle Completed -->
                <label class="flex items-center gap-2 text-gray-300 cursor-pointer">
                    <input type="checkbox" wire:model.live="showCompletedOrders" class="rounded bg-gray-700 border-gray-600 text-blue-600">
                    <span class="text-sm">Mostrar despachadas</span>
                </label>
                <!-- Current Time -->
                <div class="text-white font-mono text-lg" x-data="{ time: '' }" x-init="setInterval(() => time = new Date().toLocaleTimeString('es-CO'), 1000)">
                    <span x-text="time"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Grid -->
    <div class="flex-1 overflow-auto p-4">
        @if($totalOrders === 0)
            <div class="flex flex-col items-center justify-center h-full text-gray-500">
                <svg class="w-24 h-24 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-2xl">Sin órdenes pendientes</p>
                <p class="text-lg mt-2">Las nuevas órdenes aparecerán aquí</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
                @foreach($pendingOrders->merge($preparingOrders)->merge($readyOrders) as $order)
                    @php
                        $elapsed = $this->getElapsedTime($order->sent_to_kitchen_at);
                        $statusColor = match($order->status) {
                            \App\Enums\OrderStatus::PENDING->value => 'border-yellow-500 bg-yellow-500/10',
                            \App\Enums\OrderStatus::IN_KITCHEN->value => 'border-blue-500 bg-blue-500/10',
                            \App\Enums\OrderStatus::READY->value => 'border-green-500 bg-green-500/10',
                            default => 'border-gray-500 bg-gray-500/10',
                        };
                        if ($elapsed['urgent']) {
                            $statusColor = 'border-red-500 bg-red-500/20 animate-pulse';
                        } elseif ($elapsed['warning']) {
                            $statusColor = 'border-orange-500 bg-orange-500/10';
                        }
                    @endphp

                    <div class="rounded-xl border-2 {{ $statusColor }} overflow-hidden" wire:key="order-{{ $order->id }}">
                        <!-- Order Header -->
                        <div class="p-3 bg-gray-800 border-b border-gray-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-xl font-bold text-white">#{{ $order->order_number }}</span>
                                    @if($order->table)
                                        <span class="ml-2 px-2 py-0.5 bg-purple-600 rounded text-white text-sm">
                                            Mesa {{ $order->table->number }}
                                        </span>
                                    @else
                                        <span class="ml-2 px-2 py-0.5 bg-gray-600 rounded text-white text-sm">
                                            {{ match($order->type) {
                                                'takeaway' => 'Para Llevar',
                                                'delivery' => 'Domicilio',
                                                default => 'Mostrador'
                                            } }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-mono font-bold {{ $elapsed['urgent'] ? 'text-red-400' : ($elapsed['warning'] ? 'text-orange-400' : 'text-white') }}">
                                        {{ sprintf('%02d:%02d', $elapsed['minutes'], $elapsed['seconds']) }}
                                    </div>
                                </div>
                            </div>
                            @if($order->waiter)
                                <p class="text-gray-400 text-sm mt-1">Mesero: {{ $order->waiter->name }}</p>
                            @endif
                        </div>

                        <!-- Order Items -->
                        <div class="p-3 space-y-2 max-h-64 overflow-y-auto">
                            @foreach($order->items as $item)
                                @php
                                    $itemStatusColor = match($item->kitchen_status) {
                                        \App\Enums\KitchenStatus::PENDING->value => 'bg-yellow-600/20 border-yellow-600',
                                        \App\Enums\KitchenStatus::PREPARING->value => 'bg-blue-600/20 border-blue-600',
                                        \App\Enums\KitchenStatus::READY->value => 'bg-green-600/20 border-green-600',
                                        default => 'bg-gray-600/20 border-gray-600',
                                    };
                                @endphp
                                <div class="p-2 rounded-lg border {{ $itemStatusColor }}">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xl font-bold text-white">{{ $item->quantity }}x</span>
                                                <span class="text-white font-medium">{{ $item->product_name }}</span>
                                            </div>
                                            @if($item->modifiers && $item->modifiers->isNotEmpty())
                                                <div class="mt-1">
                                                    @foreach($item->modifiers as $mod)
                                                        <span class="text-xs text-blue-400">+ {{ $mod->name }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                            @if($item->notes)
                                                <p class="text-yellow-400 text-sm mt-1 font-medium">{{ $item->notes }}</p>
                                            @endif
                                        </div>
                                        <div class="flex gap-1">
                                            @if($item->kitchen_status === \App\Enums\KitchenStatus::PENDING->value)
                                                <button wire:click="startItem({{ $item->id }})"
                                                        class="p-2 bg-blue-600 hover:bg-blue-700 rounded-lg text-white">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </button>
                                            @elseif($item->kitchen_status === \App\Enums\KitchenStatus::PREPARING->value)
                                                <button wire:click="completeItem({{ $item->id }})"
                                                        class="p-2 bg-green-600 hover:bg-green-700 rounded-lg text-white">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </button>
                                            @else
                                                <span class="p-2 bg-green-600 rounded-lg text-white">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Order Actions -->
                        <div class="p-3 bg-gray-800 border-t border-gray-700">
                            @if($order->status === \App\Enums\OrderStatus::READY->value)
                                <button wire:click="bumpOrder({{ $order->id }})"
                                        class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg text-lg">
                                    DESPACHAR
                                </button>
                            @else
                                <button wire:click="completeOrder({{ $order->id }})"
                                        class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg">
                                    Marcar Todo Listo
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Sound Alert for new orders (optional) -->
    <div x-data="{ lastCount: {{ $pendingOrders->count() }} }"
         x-init="$watch('$wire.pendingOrders', value => {
             if (value.length > lastCount) {
                 new Audio('/sounds/new-order.mp3').play().catch(() => {});
             }
             lastCount = value.length;
         })">
    </div>
</div>
