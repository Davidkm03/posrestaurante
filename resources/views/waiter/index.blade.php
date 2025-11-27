<x-layouts.waiter title="Mesas">
    <x-slot name="navigation">
        <a href="{{ route('waiter.index') }}" class="inline-flex items-center px-1 pt-4 pb-3 border-b-2 border-blue-500 text-sm font-medium text-blue-600">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
            Mesas
        </a>
        <a href="{{ route('waiter.orders') }}" class="inline-flex items-center px-1 pt-4 pb-3 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            Mis Órdenes
        </a>
    </x-slot>

    <div class="p-4 space-y-6">
        <!-- Active Orders Summary -->
        @if($myOrders->count() > 0)
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-blue-800">
                                Tienes {{ $myOrders->count() }} {{ Str::plural('orden', $myOrders->count()) }} activa{{ $myOrders->count() > 1 ? 's' : '' }}
                            </p>
                            <p class="text-xs text-blue-600 mt-0.5">
                                {{ $myOrders->where('status', 'ready')->count() }} lista{{ $myOrders->where('status', 'ready')->count() != 1 ? 's' : '' }} para servir
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('waiter.orders') }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                        Ver todas →
                    </a>
                </div>
            </div>
        @endif

        <!-- Zones & Tables -->
        @forelse($zones as $zone)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 py-3 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900">{{ $zone->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $zone->tables->count() }} mesas</p>
                </div>

                <div class="p-4">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
                        @foreach($zone->tables as $table)
                            @php
                                $hasOrder = $table->current_order !== null;
                                $isOccupied = $table->status === 'occupied' || $hasOrder;
                                $statusColor = $hasOrder 
                                    ? 'bg-orange-50 border-orange-400 text-orange-700 hover:bg-orange-100' 
                                    : match($table->status) {
                                        'available' => 'bg-green-50 border-green-300 text-green-700 hover:bg-green-100',
                                        'occupied' => 'bg-blue-50 border-blue-300 text-blue-700 hover:bg-blue-100',
                                        'reserved' => 'bg-yellow-50 border-yellow-300 text-yellow-700 hover:bg-yellow-100',
                                        default => 'bg-gray-50 border-gray-300 text-gray-700 hover:bg-gray-100',
                                    };
                            @endphp

                            <button
                                onclick="window.location.href='{{ route('waiter.table.order', $table) }}'"
                                class="relative aspect-square border-2 rounded-xl {{ $statusColor }} transition-all active:scale-95 flex flex-col items-center justify-center p-4 touch-manipulation"
                            >
                                <!-- Table Icon -->
                                @if($hasOrder)
                                    <!-- Table with Order - Inbox Arrow Down -->
                                    <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H20"></path>
                                    </svg>
                                @else
                                    <!-- Available Table - Inbox -->
                                    <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                @endif

                                <!-- Table Number -->
                                <span class="text-2xl font-bold">{{ $table->number }}</span>

                                @if($hasOrder)
                                    <!-- Guests Count -->
                                    <div class="absolute top-2 right-2 bg-white rounded-full px-2 py-1 text-xs font-medium shadow-sm">
                                        {{ $table->current_order->guests }} <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path></svg>
                                    </div>

                                    <!-- Order Time -->
                                    <div class="absolute bottom-2 left-2 right-2">
                                        <div class="bg-white/90 backdrop-blur-sm rounded-md px-2 py-1 text-xs font-medium text-center">
                                            {{ $table->current_order->created_at->diffForHumans(null, true) }}
                                        </div>
                                    </div>
                                @endif

                                <!-- Capacity -->
                                <span class="text-xs opacity-75 mt-1">Cap: {{ $table->capacity }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                <p class="mt-4 text-lg font-medium text-gray-900">No hay mesas configuradas</p>
                <p class="mt-2 text-sm text-gray-500">Contacta al administrador para configurar las zonas y mesas</p>
            </div>
        @endforelse
    </div>
</x-layouts.waiter>
