{{-- Tarjeta de Orden Compacta para Grid --}}
@php
    use App\Enums\OrderStatus;
    
    $isPending = $order->status === OrderStatus::PENDING;
    $isPreparing = $order->status === OrderStatus::IN_PREPARATION;
    
    $elapsed = (int) now()->diffInMinutes($order->created_at, false);
    $elapsed = abs($elapsed);
    $isUrgent = $elapsed >= 20;
    $isWarning = $elapsed >= 10 && $elapsed < 20;
    
    $borderColor = $isPending 
        ? ($isUrgent ? 'border-red-500 ring-2 ring-red-500' : ($isWarning ? 'border-orange-500' : 'border-yellow-500'))
        : 'border-blue-500';
    
    $headerBg = $isPending
        ? ($isUrgent ? 'bg-red-600' : ($isWarning ? 'bg-orange-600' : 'bg-yellow-600'))
        : 'bg-blue-600';
@endphp

<div class="bg-gray-800 rounded-lg border-2 {{ $borderColor }} overflow-hidden flex flex-col {{ $isUrgent ? 'animate-pulse' : '' }}" 
     wire:key="order-{{ $order->id }}">
    
    {{-- Header --}}
    <div class="px-3 py-2 {{ $headerBg }} flex items-center justify-between">
        <div>
            <span class="text-lg font-bold">
                @if($order->table)
                    Mesa {{ $order->table->number }}
                @else
                    {{ $order->type->value === 'delivery' ? 'DELIVERY' : 'LLEVAR' }}
                @endif
            </span>
            <span class="text-xs opacity-75 block">#{{ $order->order_number }}</span>
        </div>
        <div class="text-right">
            <span class="text-2xl font-bold font-mono {{ $isUrgent ? 'text-white' : '' }}">{{ $elapsed }}</span>
            <span class="text-xs block">min</span>
        </div>
    </div>
    
    {{-- Items --}}
    <div class="flex-1 p-3 space-y-2 overflow-y-auto max-h-48">
        @foreach($order->items as $item)
            <div class="{{ $item->is_ready ? 'opacity-50' : '' }}">
                <div class="flex items-start gap-2">
                    <span class="text-blue-400 font-bold">{{ intval($item->quantity) }}x</span>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium {{ $item->is_ready ? 'line-through' : '' }}">{{ $item->product_name }}</span>
                        
                        {{-- Si es un combo, mostrar los productos incluidos --}}
                        @if($item->product && $item->product->is_combo && $item->product->comboProducts->count() > 0)
                            <div class="text-xs text-purple-400 mt-1 pl-2 border-l-2 border-purple-500">
                                @foreach($item->product->comboProducts as $comboProduct)
                                    <div class="flex items-center gap-1">
                                        <span class="text-purple-300">{{ $comboProduct->pivot->quantity ?? 1 }}x</span>
                                        <span>{{ $comboProduct->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        
                        {{-- Modificadores --}}
                        @if($item->modifiers && $item->modifiers->count() > 0)
                            <div class="text-xs text-cyan-400 mt-0.5">
                                @foreach($item->modifiers as $modifier)
                                    <span class="inline-block mr-1">+ {{ $modifier->name }}</span>
                                @endforeach
                            </div>
                        @endif
                        
                        {{-- Notas --}}
                        @if($item->notes)
                            <span class="text-xs text-orange-400 block truncate">* {{ $item->notes }}</span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    {{-- Acción --}}
    <div class="p-2 bg-gray-900">
        @if($isPending)
            <button wire:click="startPreparation({{ $order->id }})"
                    class="w-full py-2 bg-blue-600 hover:bg-blue-700 rounded font-bold text-sm transition-colors">
                PREPARAR
            </button>
        @else
            <button wire:click="completeOrder({{ $order->id }})"
                    class="w-full py-2 bg-green-600 hover:bg-green-700 rounded font-bold text-sm transition-colors">
                LISTO
            </button>
        @endif
    </div>
</div>
