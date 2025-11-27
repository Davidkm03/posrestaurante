{{-- Sidebar de Órdenes Listas --}}
<div class="w-72 bg-green-900/30 border-l-4 border-green-500 flex flex-col">
    {{-- Header --}}
    <div class="p-3 bg-green-800/50 border-b border-green-700">
        <h2 class="text-lg font-bold text-green-400 flex items-center gap-2">
            <span class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></span>
            LISTOS PARA SERVIR
            <span class="ml-auto bg-green-600 px-2 py-0.5 rounded-full text-sm">{{ $readyOrders->count() }}</span>
        </h2>
    </div>
    
    {{-- Lista de Órdenes Listas --}}
    <div class="flex-1 overflow-y-auto p-2 space-y-2">
        @foreach($readyOrders as $order)
            <div wire:click="bumpOrder({{ $order->id }})"
                 class="bg-green-800/50 border border-green-600 rounded-lg p-3 cursor-pointer hover:bg-green-700/50 transition-all group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xl font-bold text-green-300">
                            @if($order->table)
                                Mesa {{ $order->table->number }}
                            @else
                                {{ $order->type->value === 'delivery' ? 'Delivery' : 'Para llevar' }}
                            @endif
                        </p>
                        <p class="text-xs text-green-400/70">#{{ $order->order_number }}</p>
                    </div>
                    <div class="text-center">
                        <svg class="w-8 h-8 text-green-400 group-hover:scale-110 transition-transform mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="text-xs text-green-400">Despachar</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
