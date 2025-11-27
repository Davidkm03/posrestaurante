<x-layouts.kitchen title="Pantalla de Cocina">
    <div class="flex-1 overflow-hidden p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 h-full overflow-y-auto">
            @forelse($orders as $order)
                @php
                    $minutesElapsed = $order->created_at->diffInMinutes(now());
                    $urgentClass = $minutesElapsed > 15 ? 'urgent' : ($minutesElapsed > 10 ? 'warning' : '');
                @endphp
                
                <div class="order-card {{ $urgentClass }} bg-gray-800 flex flex-col" style="max-height: 500px;">
                    <!-- Order Header -->
                    <div class="p-4 bg-{{ $order->status->color() }}-600 flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-2xl font-bold">#{{ $order->order_number }}</span>
                                @if($order->table)
                                    <span class="px-2 py-1 bg-white bg-opacity-20 rounded text-sm">
                                        Mesa {{ $order->table->number }}
                                    </span>
                                @endif
                            </div>
                            <div class="text-sm opacity-90 mt-1">
                                {{ $order->created_at->format('H:i') }} 
                                <span class="font-semibold">({{ number_format($minutesElapsed, 0) }} min)</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs opacity-75">{{ $order->type->label() }}</div>
                            <div class="text-lg font-bold">{{ $order->status->label() }}</div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="flex-1 overflow-y-auto p-4">
                        @foreach($order->items as $item)
                            <div class="order-item {{ $item->is_ready ? 'completed' : '' }}">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xl font-bold text-white">{{ intval($item->quantity) }}x</span>
                                            <span class="kitchen-text-lg text-white">{{ $item->name ?? $item->product->name }}</span>
                                        </div>
                                        @if($item->notes)
                                            <div class="mt-1 pl-8 text-yellow-400 kitchen-text-lg">
                                                📝 {{ $item->notes }}
                                            </div>
                                        @endif
                                        @if($item->modifiers && count($item->modifiers) > 0)
                                            <div class="mt-1 pl-8 text-gray-400 text-sm">
                                                @foreach($item->modifiers as $modifier)
                                                    <div>+ {{ $modifier['name'] }}</div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    @if(!$item->is_ready)
                                        <form action="{{ route('kitchen.item.ready', [$order, $item]) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="ml-2 p-2 bg-green-600 hover:bg-green-700 rounded-lg">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <div class="ml-2 p-2 bg-gray-600 rounded-lg">
                                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Order Actions -->
                    <div class="p-4 bg-gray-900 border-t border-gray-700 flex gap-2">
                        @if($order->status === 'pending')
                            <form action="{{ route('kitchen.order.update-status', $order) }}" method="POST" class="flex-1">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="in_preparation">
                                <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 rounded-lg font-semibold">
                                    Iniciar Preparación
                                </button>
                            </form>
                        @endif

                        @if($order->status === 'in_preparation')
                            <form action="{{ route('kitchen.order.update-status', $order) }}" method="POST" class="flex-1">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="ready">
                                <button type="submit" class="w-full py-3 bg-green-600 hover:bg-green-700 rounded-lg font-semibold">
                                    Marcar Listo
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full flex flex-col items-center justify-center h-full text-gray-500">
                    <svg class="w-24 h-24 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-2xl font-semibold">No hay órdenes pendientes</p>
                    <p class="text-lg mt-2">Todas las órdenes están completadas</p>
                </div>
            @endforelse
        </div>
    </div>

    @push('scripts')
    <script>
        // Escuchar eventos de WebSocket para nuevas órdenes
        if (typeof window.Echo !== 'undefined') {
            const branchId = '{{ session("current_branch_id") }}';
            
            // Canal de cocina
            window.Echo.channel(`kitchen.${branchId}`)
                .listen('.order.created', (e) => {
                    console.log('Nueva orden recibida:', e);
                    
                    // Reproducir sonido de notificación
                    playNotificationSound();
                    
                    // Recargar página para mostrar nueva orden
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                })
                .listen('.order.status.updated', (e) => {
                    console.log('Orden actualizada:', e);
                    
                    // Si la orden fue completada, refrescar
                    if (e.new_status === 'completed' || e.new_status === 'cancelled') {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                });
            
            function playNotificationSound() {
                // Crear beep simple con Web Audio API
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.frequency.value = 800;
                oscillator.type = 'sine';
                
                gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
                
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.5);
            }
        } else {
            console.warn('Laravel Echo no está inicializado. WebSockets deshabilitados.');
        }
    </script>
    @endpush
</x-layouts.kitchen>
