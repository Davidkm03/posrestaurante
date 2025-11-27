<x-layouts.waiter title="Mis Órdenes">
    <x-slot name="navigation">
        <a href="{{ route('waiter.index') }}" class="inline-flex items-center px-1 pt-4 pb-3 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
            Mesas
        </a>
        <a href="{{ route('waiter.orders') }}" class="inline-flex items-center px-1 pt-4 pb-3 border-b-2 border-blue-500 text-sm font-medium text-blue-600">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            Mis Órdenes
        </a>
    </x-slot>

    <div class="p-4">
        @if($orders->count() > 0)
            <div class="space-y-3">
                @foreach($orders as $order)
                    @php
                        $statusConfig = match($order->status) {
                            'pending' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'text' => 'text-yellow-700', 'badge' => 'yellow'],
                            'in_preparation' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-700', 'badge' => 'blue'],
                            'ready' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'text' => 'text-green-700', 'badge' => 'green'],
                            'delivered' => ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'text' => 'text-gray-700', 'badge' => 'secondary'],
                            default => ['bg' => 'bg-white', 'border' => 'border-gray-200', 'text' => 'text-gray-700', 'badge' => 'secondary'],
                        };
                    @endphp

                    <div class="{{ $statusConfig['bg'] }} border-2 {{ $statusConfig['border'] }} rounded-xl overflow-hidden shadow-sm">
                        <!-- Header -->
                        <div class="p-4 border-b {{ $statusConfig['border'] }}">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <h3 class="font-mono text-lg font-bold {{ $statusConfig['text'] }}">{{ $order->order_number }}</h3>
                                    <p class="text-sm text-gray-600">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <x-badge :type="$statusConfig['badge']" size="lg" dot>
                                    {{ $order->status_label }}
                                </x-badge>
                            </div>

                            <div class="flex items-center gap-4 text-sm">
                                @if($order->table)
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="font-medium">Mesa {{ $order->table->number }}</span>
                                        <span class="text-gray-500">({{ $order->table->zone->name }})</span>
                                    </div>
                                @endif

                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path>
                                    </svg>
                                    <span>{{ $order->guests }} {{ Str::plural('persona', $order->guests) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Items -->
                        <div class="p-4">
                            <div class="space-y-2">
                                @foreach($order->items as $item)
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-blue-600">{{ intval($item->quantity) }}x</span>
                                                <span class="font-medium">{{ $item->name }}</span>
                                            </div>
                                            @if($item->modifiers->isNotEmpty())
                                                <div class="ml-8 mt-1 space-y-0.5">
                                                    @foreach($item->modifiers as $mod)
                                                        <p class="text-sm text-gray-600">+ {{ $mod->name }}</p>
                                                    @endforeach
                                                </div>
                                            @endif
                                            @if($item->notes)
                                                <p class="ml-8 mt-1 text-sm text-orange-600 italic">{{ $item->notes }}</p>
                                            @endif
                                        </div>
                                        <span class="font-semibold">${{ number_format($item->total, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Total -->
                            <div class="mt-4 pt-3 border-t {{ $statusConfig['border'] }} flex items-center justify-between">
                                <span class="text-lg font-bold">Total</span>
                                <span class="text-2xl font-bold {{ $statusConfig['text'] }}">${{ number_format($order->total, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        @if($order->status === 'ready')
                            <div class="p-4 bg-white/50 border-t {{ $statusConfig['border'] }}">
                                <form method="POST" action="{{ route('waiter.orders.deliver', $order) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 active:scale-95 transition-all flex items-center justify-center gap-2 touch-manipulation">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Marcar como Servido
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($orders->hasPages())
                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            @endif
        @else
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <p class="mt-4 text-lg font-medium text-gray-900">No tienes órdenes activas</p>
                <p class="mt-2 text-sm text-gray-500">Tus órdenes aparecerán aquí</p>
                <a href="{{ route('waiter.index') }}" class="mt-6 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Ver Mesas
                </a>
            </div>
        @endif
    </div>
</x-layouts.waiter>
