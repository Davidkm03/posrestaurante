{{-- Tarjeta de Orden (Versión Completa) --}}
@php
    use App\Enums\OrderStatus;
    
    $elapsed = (int) abs(now()->diffInMinutes($order->created_at, false));
    $isUrgent = $elapsed >= 20;
    $isWarning = $elapsed >= 10 && $elapsed < 20;
    
    $isPending = $order->status === OrderStatus::PENDING;
    $isPreparing = $order->status === OrderStatus::IN_PREPARATION;
    $isReady = $order->status === OrderStatus::READY;
    
    $borderColor = match(true) {
        $isReady => 'border-green-500',
        $isPreparing => 'border-blue-500',
        $isUrgent => 'border-red-500',
        $isWarning => 'border-orange-500',
        default => 'border-yellow-500',
    };
    
    $headerBg = match(true) {
        $isReady => 'bg-green-600',
        $isPreparing => 'bg-blue-600',
        $isUrgent => 'bg-red-600',
        $isWarning => 'bg-orange-600',
        default => 'bg-yellow-600',
    };
@endphp

<div class="bg-gray-800 rounded-xl border-2 {{ $borderColor }} overflow-hidden {{ $isUrgent && !$isReady ? 'animate-pulse' : '' }}"
     wire:key="order-{{ $order->id }}">
    
    {{-- Header --}}
    <div class="px-4 py-3 {{ $headerBg }}">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-2xl font-bold">
                    @if($order->table)
                        Mesa {{ $order->table->number }}
                    @else
                        {{ $order->type->value === 'delivery' ? 'Delivery' : 'Para llevar' }}
                    @endif
                </p>
                <p class="text-sm opacity-80">#{{ $order->order_number }}</p>
            </div>
            <div class="text-right">
                <p class="text-3xl font-bold font-mono">{{ $elapsed }}</p>
                <p class="text-xs opacity-80">minutos</p>
            </div>
        </div>
    </div>
    
    {{-- Items --}}
    <div class="p-4 space-y-2 max-h-64 overflow-y-auto">
        @foreach($order->items as $item)
            <div class="flex items-start gap-3 py-2 border-b border-gray-700 last:border-0 {{ $item->is_ready ? 'opacity-50' : '' }}">
                <span class="text-xl font-bold text-blue-400">{{ intval($item->quantity) }}x</span>
                <div class="flex-1">
                    <span class="font-medium {{ $item->is_ready ? 'line-through' : '' }}">{{ $item->product_name }}</span>
                    
                    {{-- Modificadores --}}
                    @if($item->modifiers && $item->modifiers->count() > 0)
                        <div class="text-sm text-cyan-400 mt-1">
                            @foreach($item->modifiers as $modifier)
                                <span class="inline-block mr-2">+ {{ $modifier->name }}</span>
                            @endforeach
                        </div>
                    @endif
                    
                    {{-- Notas --}}
                    @if($item->notes)
                        <p class="text-sm text-orange-400 mt-1">Nota: {{ $item->notes }}</p>
                    @endif
                </div>
                @if(!$item->is_ready && !$isReady)
                    <button wire:click="completeItem({{ $item->id }})"
                            class="p-2 bg-gray-700 hover:bg-green-600 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </button>
                @elseif($item->is_ready)
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                @endif
            </div>
        @endforeach
    </div>
    
    {{-- Acción --}}
    <div class="p-3 bg-gray-900">
        @if($isPending)
            <button wire:click="startPreparation({{ $order->id }})"
                    class="w-full py-3 bg-blue-600 hover:bg-blue-700 rounded-lg font-bold transition-colors flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                </svg>
                INICIAR PREPARACIÓN
            </button>
        @elseif($isPreparing)
            <button wire:click="completeOrder({{ $order->id }})"
                    class="w-full py-3 bg-green-600 hover:bg-green-700 rounded-lg font-bold transition-colors flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                MARCAR TODO LISTO
            </button>
        @elseif($isReady)
            <button wire:click="bumpOrder({{ $order->id }})"
                    class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 rounded-lg font-bold transition-colors flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                DESPACHAR
            </button>
        @endif
    </div>
</div>
