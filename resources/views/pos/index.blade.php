<x-layouts.pos title="POS">
    <div class="flex-1 flex overflow-hidden">
        <!-- Left Panel - Tables/Zones -->
        <div class="w-2/3 flex flex-col bg-gray-900 overflow-hidden">
            <!-- Zone Tabs -->
            <div class="flex-shrink-0 bg-gray-800 border-b border-gray-700">
                <div class="flex items-center gap-1 p-2 overflow-x-auto scrollbar-hide">
                    <button
                        class="px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white"
                        data-zone="all"
                    >
                        Todas
                    </button>
                    @foreach($zones as $zone)
                        <button
                            class="px-4 py-2 text-sm font-medium rounded-lg text-gray-400 hover:bg-gray-700 hover:text-white transition-colors"
                            data-zone="{{ $zone->id }}"
                            style="border-left: 3px solid {{ $zone->color }}"
                        >
                            {{ $zone->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Tables Grid -->
            <div class="flex-1 overflow-y-auto p-4">
                <div class="grid grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4" id="tables-grid">
                    @foreach($zones as $zone)
                        @foreach($zone->tables as $table)
                            <a
                                href="{{ route('pos.table', $table) }}"
                                class="pos-product-card relative bg-gray-800 rounded-xl p-4 flex flex-col items-center justify-center border-2 transition-all
                                    @if($table->status === 'occupied') border-red-500 bg-red-900/20
                                    @elseif($table->status === 'reserved') border-yellow-500 bg-yellow-900/20
                                    @else border-gray-700 hover:border-blue-500
                                    @endif"
                                data-zone="{{ $zone->id }}"
                            >
                                <!-- Zone indicator -->
                                <div class="absolute top-2 left-2 w-3 h-3 rounded-full" style="background-color: {{ $zone->color }}"></div>

                                <!-- Table Number -->
                                <span class="text-3xl font-bold text-white mb-1">{{ $table->number }}</span>

                                <!-- Capacity -->
                                <div class="flex items-center gap-1 text-gray-400 text-xs mb-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    {{ $table->capacity }}
                                </div>

                                <!-- Status -->
                                @if($table->status === 'occupied' && $table->currentOrder)
                                    <div class="text-center">
                                        <p class="text-xs text-red-400">
                                            ${{ number_format($table->currentOrder->total, 0, ',', '.') }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $table->currentOrder->created_at->diffForHumans(null, true) }}
                                        </p>
                                    </div>
                                @else
                                    <span class="text-xs text-green-400 font-medium">Libre</span>
                                @endif
                            </a>
                        @endforeach
                    @endforeach
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="flex-shrink-0 bg-gray-800 border-t border-gray-700 p-3">
                <div class="flex items-center gap-3">
                    <a href="{{ route('pos.quick-sale') }}" class="flex-1 pos-btn flex items-center justify-center gap-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Venta Rápida
                    </a>
                    <a href="{{ route('pos.takeaway') }}" class="flex-1 pos-btn flex items-center justify-center gap-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                        </svg>
                        Para Llevar
                    </a>
                    <a href="{{ route('pos.delivery') }}" class="flex-1 pos-btn flex items-center justify-center gap-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        </svg>
                        Domicilio
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Panel - Pending Orders -->
        <div class="w-1/3 flex flex-col bg-gray-800 border-l border-gray-700">
            <div class="flex-shrink-0 px-4 py-3 border-b border-gray-700">
                <h2 class="text-lg font-semibold text-white">Órdenes Pendientes</h2>
                <p class="text-sm text-gray-400">{{ $pendingOrders->count() }} órdenes activas</p>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-3">
                @forelse($pendingOrders as $order)
                    <a href="{{ route('pos.table', $order->table_id ?? 0) }}" class="block bg-gray-700 rounded-lg p-3 hover:bg-gray-600 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-mono text-sm text-blue-400">{{ $order->order_number }}</span>
                            <span class="text-xs px-2 py-1 rounded-full
                                @if($order->status === 'pending') bg-yellow-600 text-yellow-100
                                @else bg-blue-600 text-blue-100
                                @endif">
                                {{ $order->status === 'pending' ? 'Pendiente' : 'Preparando' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-300">
                                @if($order->table)
                                    Mesa {{ $order->table->number }}
                                @else
                                    {{ ucfirst($order->type) }}
                                @endif
                            </span>
                            <span class="font-semibold text-white">${{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between mt-2 text-xs text-gray-400">
                            <span>{{ $order->waiter->name ?? 'Sin mesero' }}</span>
                            <span>{{ $order->created_at->diffForHumans() }}</span>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-gray-400">No hay órdenes pendientes</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Zone filter
        document.querySelectorAll('[data-zone]').forEach(button => {
            if (button.tagName === 'BUTTON') {
                button.addEventListener('click', function() {
                    const zone = this.dataset.zone;

                    // Update active state
                    document.querySelectorAll('button[data-zone]').forEach(btn => {
                        btn.classList.remove('bg-blue-600', 'text-white');
                        btn.classList.add('text-gray-400');
                    });
                    this.classList.remove('text-gray-400');
                    this.classList.add('bg-blue-600', 'text-white');

                    // Filter tables
                    document.querySelectorAll('#tables-grid > a').forEach(table => {
                        if (zone === 'all' || table.dataset.zone === zone) {
                            table.style.display = '';
                        } else {
                            table.style.display = 'none';
                        }
                    });
                });
            }
        });
    </script>
    @endpush
</x-layouts.pos>
