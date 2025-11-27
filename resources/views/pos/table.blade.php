<x-layouts.pos :title="'Mesa ' . $table->number">
    <div class="flex-1 flex overflow-hidden">
        <!-- Left Panel - Products -->
        <div class="w-2/3 flex flex-col bg-gray-900">
            <!-- Header con info de mesa -->
            <div class="flex-shrink-0 bg-gray-800 border-b border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('pos.index') }}" class="text-gray-400 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </a>
                        <div>
                            <h2 class="text-xl font-bold text-white">Mesa {{ $table->number }}</h2>
                            <p class="text-sm text-gray-400">{{ $table->zone->name }} • Capacidad: {{ $table->capacity }} personas</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            {{ $table->status === 'free' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                            {{ match($table->status) {
                                'free' => 'Libre',
                                'occupied' => 'Ocupada',
                                'reserved' => 'Reservada',
                                'cleaning' => 'Limpiando',
                                default => $table->status
                            } }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Product Grid Component -->
            @livewire('pos.product-grid')
        </div>

        <!-- Right Panel - Cart Component -->
        <div class="w-1/3 flex flex-col bg-gray-800 border-l border-gray-700">
            @livewire('pos.cart', [
                'tableId' => $table->id,
                'orderId' => $currentOrder?->id
            ])
        </div>
    </div>

    <!-- Payment Modal Component -->
    @livewire('pos.payment-modal')
</x-layouts.pos>
