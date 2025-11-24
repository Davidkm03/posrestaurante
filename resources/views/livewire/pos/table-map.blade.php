<div class="h-full flex flex-col bg-gray-900">
    <!-- Header with Stats -->
    <div class="flex-shrink-0 bg-gray-800 border-b border-gray-700 p-4">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-white">Mapa de Mesas</h2>
            <div class="flex items-center gap-4">
                <!-- Stats -->
                <div class="flex gap-3 text-sm">
                    <span class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-green-500"></span>
                        <span class="text-gray-300">Libres: {{ $stats['free'] }}</span>
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-red-500"></span>
                        <span class="text-gray-300">Ocupadas: {{ $stats['occupied'] }}</span>
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                        <span class="text-gray-300">Reservadas: {{ $stats['reserved'] }}</span>
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                        <span class="text-gray-300">Limpieza: {{ $stats['cleaning'] }}</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Zone Tabs -->
        <div class="flex gap-2 mt-4 overflow-x-auto">
            @foreach($zones as $zone)
                <button wire:click="selectZone({{ $zone['id'] }})"
                        class="flex-shrink-0 px-4 py-2 rounded-lg font-medium transition-colors {{ $selectedZoneId === $zone['id'] ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }}">
                    {{ $zone['name'] }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Tables Grid -->
    <div class="flex-1 overflow-auto p-6">
        @if($tables->isEmpty())
            <div class="flex flex-col items-center justify-center h-full text-gray-500">
                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <p class="text-lg">No hay mesas en esta zona</p>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                @foreach($tables as $table)
                    @php
                        $statusConfig = match($table->status) {
                            \App\Enums\TableStatus::FREE->value => [
                                'bg' => 'bg-green-600 hover:bg-green-500',
                                'border' => 'border-green-500',
                                'icon' => 'check',
                            ],
                            \App\Enums\TableStatus::OCCUPIED->value => [
                                'bg' => 'bg-red-600 hover:bg-red-500',
                                'border' => 'border-red-500',
                                'icon' => 'users',
                            ],
                            \App\Enums\TableStatus::RESERVED->value => [
                                'bg' => 'bg-yellow-600 hover:bg-yellow-500',
                                'border' => 'border-yellow-500',
                                'icon' => 'clock',
                            ],
                            \App\Enums\TableStatus::CLEANING->value => [
                                'bg' => 'bg-blue-600 hover:bg-blue-500',
                                'border' => 'border-blue-500',
                                'icon' => 'sparkles',
                            ],
                            default => [
                                'bg' => 'bg-gray-600 hover:bg-gray-500',
                                'border' => 'border-gray-500',
                                'icon' => 'question',
                            ],
                        };
                    @endphp

                    <button wire:click="selectTable({{ $table->id }})"
                            wire:key="table-{{ $table->id }}"
                            class="aspect-square rounded-xl border-2 {{ $statusConfig['border'] }} {{ $statusConfig['bg'] }} p-4 flex flex-col items-center justify-center transition-all transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-white/50">
                        <span class="text-3xl font-bold text-white">{{ $table->number }}</span>
                        <span class="text-sm text-white/80 mt-1">{{ $table->capacity }} pers.</span>

                        @if($table->currentOrder)
                            <div class="mt-2 text-center">
                                <span class="text-xs text-white/70">#{{ $table->currentOrder->order_number }}</span>
                                <p class="text-white font-bold">${{ number_format($table->currentOrder->total, 0, ',', '.') }}</p>
                                @if($table->currentOrder->waiter)
                                    <span class="text-xs text-white/70">{{ $table->currentOrder->waiter->name }}</span>
                                @endif
                            </div>
                        @endif
                    </button>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Table Detail Modal -->
    @if($showTableModal && $selectedTable)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70" wire:click.self="closeTableModal">
            <div class="bg-gray-800 rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
                <!-- Modal Header -->
                <div class="p-4 border-b border-gray-700 {{ match($selectedTable->status) {
                    \App\Enums\TableStatus::FREE->value => 'bg-green-600',
                    \App\Enums\TableStatus::OCCUPIED->value => 'bg-red-600',
                    \App\Enums\TableStatus::RESERVED->value => 'bg-yellow-600',
                    \App\Enums\TableStatus::CLEANING->value => 'bg-blue-600',
                    default => 'bg-gray-600',
                } }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold text-white">Mesa {{ $selectedTable->number }}</h3>
                            <p class="text-white/80">{{ $selectedTable->zone->name }} - {{ $selectedTable->capacity }} personas</p>
                        </div>
                        <button wire:click="closeTableModal" class="p-2 text-white/80 hover:text-white rounded-lg hover:bg-white/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="p-4">
                    @if($selectedTable->currentOrder)
                        <!-- Order Info -->
                        <div class="bg-gray-700/50 rounded-lg p-4 mb-4">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <span class="text-gray-400 text-sm">Orden Activa</span>
                                    <p class="text-white font-bold text-lg">#{{ $selectedTable->currentOrder->order_number }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-gray-400 text-sm">Total</span>
                                    <p class="text-green-400 font-bold text-xl">${{ number_format($selectedTable->currentOrder->total, 0, ',', '.') }}</p>
                                </div>
                            </div>

                            @if($selectedTable->currentOrder->waiter)
                                <p class="text-gray-400 text-sm">Mesero: <span class="text-white">{{ $selectedTable->currentOrder->waiter->name }}</span></p>
                            @endif

                            <p class="text-gray-400 text-sm mt-1">
                                Items: <span class="text-white">{{ $selectedTable->currentOrder->items->sum('quantity') }}</span>
                            </p>

                            @php
                                $orderTime = $selectedTable->currentOrder->created_at->diffForHumans();
                            @endphp
                            <p class="text-gray-400 text-sm mt-1">
                                Abierta: <span class="text-white">{{ $orderTime }}</span>
                            </p>
                        </div>

                        <!-- Actions for Occupied Table -->
                        <div class="space-y-2">
                            <button wire:click="continueOrder" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Agregar Productos
                            </button>

                            <div class="grid grid-cols-2 gap-2">
                                <button wire:click="printBill" class="py-2 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-lg text-sm">
                                    Imprimir Cuenta
                                </button>
                                <button wire:click="goToPayment" class="py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg text-sm">
                                    Cobrar
                                </button>
                            </div>

                            <div class="grid grid-cols-3 gap-2 pt-2 border-t border-gray-700">
                                <button wire:click="transferTable" class="py-2 bg-gray-700 hover:bg-gray-600 text-white text-xs rounded-lg">
                                    Transferir
                                </button>
                                <button wire:click="mergeTable" class="py-2 bg-gray-700 hover:bg-gray-600 text-white text-xs rounded-lg">
                                    Unir Mesas
                                </button>
                                <button wire:click="splitOrder" class="py-2 bg-gray-700 hover:bg-gray-600 text-white text-xs rounded-lg">
                                    Dividir Cuenta
                                </button>
                            </div>
                        </div>
                    @elseif($selectedTable->status === \App\Enums\TableStatus::FREE->value)
                        <!-- Actions for Free Table -->
                        <div class="text-center py-4">
                            <p class="text-gray-400 mb-4">Mesa disponible</p>
                            <button wire:click="openTable" class="w-full py-4 bg-green-600 hover:bg-green-700 text-white text-xl font-bold rounded-lg">
                                Abrir Mesa
                            </button>
                        </div>
                    @elseif($selectedTable->status === \App\Enums\TableStatus::RESERVED->value)
                        <!-- Actions for Reserved Table -->
                        <div class="text-center py-4">
                            <p class="text-yellow-400 mb-4">Mesa reservada</p>
                            <button wire:click="openTable" class="w-full py-4 bg-green-600 hover:bg-green-700 text-white text-lg font-bold rounded-lg">
                                Iniciar Servicio
                            </button>
                        </div>
                    @elseif($selectedTable->status === \App\Enums\TableStatus::CLEANING->value)
                        <!-- Actions for Cleaning Table -->
                        <div class="text-center py-4">
                            <p class="text-blue-400 mb-4">Mesa en limpieza</p>
                            <button wire:click="cleanTable" class="w-full py-4 bg-green-600 hover:bg-green-700 text-white text-lg font-bold rounded-lg">
                                Marcar como Lista
                            </button>
                        </div>
                    @endif

                    @if($selectedTable->status === \App\Enums\TableStatus::OCCUPIED->value)
                        <div class="mt-4 pt-4 border-t border-gray-700">
                            <button wire:click="markForCleaning" wire:confirm="¿Marcar mesa para limpieza?" class="w-full py-2 bg-gray-700 hover:bg-gray-600 text-gray-300 text-sm rounded-lg">
                                Cerrar y Limpiar Mesa
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
