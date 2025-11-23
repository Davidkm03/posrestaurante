<x-layouts.kitchen>
    <div class="h-full flex gap-4">
        <!-- Pending Orders -->
        <div class="flex-1 flex flex-col">
            <h2 class="text-lg font-bold text-yellow-400 mb-3 flex items-center gap-2">
                <span class="w-3 h-3 bg-yellow-400 rounded-full animate-pulse"></span>
                PENDIENTES ({{ $stats['pending_count'] }})
            </h2>
            <div class="flex-1 overflow-y-auto space-y-3">
                @forelse($pendingOrders as $order)
                    @include('kitchen.partials.order-card', ['order' => $order, 'status' => 'pending'])
                @empty
                    <div class="flex items-center justify-center h-full">
                        <p class="text-gray-500 text-lg">Sin órdenes pendientes</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- In Preparation -->
        <div class="flex-1 flex flex-col">
            <h2 class="text-lg font-bold text-blue-400 mb-3 flex items-center gap-2">
                <span class="w-3 h-3 bg-blue-400 rounded-full animate-pulse"></span>
                EN PREPARACIÓN ({{ $stats['preparing_count'] }})
            </h2>
            <div class="flex-1 overflow-y-auto space-y-3">
                @forelse($preparingOrders as $order)
                    @include('kitchen.partials.order-card', ['order' => $order, 'status' => 'preparing'])
                @empty
                    <div class="flex items-center justify-center h-full">
                        <p class="text-gray-500 text-lg">Sin órdenes en preparación</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Ready Orders -->
        <div class="w-80 flex flex-col">
            <h2 class="text-lg font-bold text-green-400 mb-3 flex items-center gap-2">
                <span class="w-3 h-3 bg-green-400 rounded-full"></span>
                LISTOS ({{ $stats['ready_count'] }})
            </h2>
            <div class="flex-1 overflow-y-auto space-y-2">
                @forelse($readyOrders as $order)
                    <div class="bg-green-900/50 border border-green-700 rounded-lg p-3 cursor-pointer hover:bg-green-900/70 transition-colors"
                         onclick="bumpOrder({{ $order->id }})">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-bold text-green-400 text-lg">
                                    @if($order->table)
                                        Mesa {{ $order->table->number }}
                                    @else
                                        {{ ucfirst($order->type) }}
                                    @endif
                                </p>
                                <p class="text-sm text-gray-400">{{ $order->order_number }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-green-400 font-bold">LISTO</p>
                                <p class="text-xs text-gray-400">{{ $order->ready_at?->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex items-center justify-center h-32">
                        <p class="text-gray-500">Sin órdenes listas</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function startPreparation(orderId) {
            fetch(`/kitchen/orders/${orderId}/start`, {
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

        function markReady(orderId) {
            fetch(`/kitchen/orders/${orderId}/ready`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Play sound
                    document.getElementById('notification-sound')?.play();
                    location.reload();
                }
            });
        }

        function bumpOrder(orderId) {
            fetch(`/kitchen/orders/${orderId}/bump`, {
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

        // Auto refresh every 30 seconds
        setInterval(() => {
            location.reload();
        }, 30000);
    </script>
    @endpush
</x-layouts.kitchen>
