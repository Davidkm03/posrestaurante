@php
    $elapsed = $order->created_at->diffInMinutes(now());
    $isUrgent = $elapsed > 20;
    $isWarning = $elapsed > 10 && !$isUrgent;
@endphp

<div class="order-card {{ $isUrgent ? 'urgent' : ($isWarning ? 'warning' : '') }} bg-gray-800 border border-gray-700">
    <!-- Header -->
    <div class="px-4 py-3 {{ $status === 'pending' ? 'bg-yellow-900/50' : 'bg-blue-900/50' }}">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-2xl font-bold text-white">
                    @if($order->table)
                        Mesa {{ $order->table->number }}
                    @else
                        {{ strtoupper($order->type) }}
                    @endif
                </p>
                <p class="text-sm text-gray-400">{{ $order->order_number }}</p>
            </div>
            <div class="text-right">
                <p class="text-3xl font-bold {{ $isUrgent ? 'text-red-400' : ($isWarning ? 'text-yellow-400' : 'text-white') }}">
                    {{ $elapsed }}<span class="text-lg">min</span>
                </p>
                <p class="text-xs text-gray-400">{{ $order->created_at->format('H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Items -->
    <div class="p-4 space-y-2">
        @foreach($order->items as $item)
            <div class="order-item flex items-start justify-between {{ $item->status === 'ready' ? 'completed' : '' }}">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <span class="text-xl font-bold text-blue-400">{{ $item->quantity }}x</span>
                        <span class="kitchen-text-lg text-white font-medium">{{ $item->product_name }}</span>
                    </div>
                    @if($item->modifiers->isNotEmpty())
                        <div class="ml-8 mt-1">
                            @foreach($item->modifiers as $modifier)
                                <p class="text-sm text-yellow-400">+ {{ $modifier->name }}</p>
                            @endforeach
                        </div>
                    @endif
                    @if($item->notes)
                        <p class="ml-8 mt-1 text-sm text-orange-400 italic">{{ $item->notes }}</p>
                    @endif
                </div>
                @if($item->status !== 'ready')
                    <button
                        onclick="markItemReady({{ $item->id }})"
                        class="p-2 bg-gray-700 hover:bg-green-600 rounded-lg transition-colors"
                        title="Marcar como listo"
                    >
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </button>
                @else
                    <span class="text-green-400">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"></path>
                        </svg>
                    </span>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Footer Actions -->
    <div class="px-4 py-3 bg-gray-900/50 flex gap-2">
        @if($status === 'pending')
            <button
                onclick="startPreparation({{ $order->id }})"
                class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition-colors text-lg"
            >
                INICIAR PREPARACIÓN
            </button>
        @else
            <button
                onclick="markReady({{ $order->id }})"
                class="flex-1 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition-colors text-lg"
            >
                MARCAR TODO LISTO
            </button>
        @endif
    </div>
</div>

<script>
function markItemReady(itemId) {
    fetch(`/kitchen/items/${itemId}/ready`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
