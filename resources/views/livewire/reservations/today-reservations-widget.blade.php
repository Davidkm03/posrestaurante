<div class="bg-white rounded-xl shadow-sm border overflow-hidden" wire:poll.60s>
    <!-- Header -->
    <div class="px-4 py-3 border-b bg-gradient-to-r from-purple-600 to-purple-700">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="font-semibold text-white">Reservaciones Hoy</h3>
            </div>
            <a href="{{ route('admin.reservations.index') }}" class="text-white/80 hover:text-white text-sm">
                Ver todas
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-3 divide-x border-b">
        <div class="p-3 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $totalToday }}</p>
            <p class="text-xs text-gray-500">Total</p>
        </div>
        <div class="p-3 text-center">
            <p class="text-2xl font-bold text-yellow-600">{{ $pendingCount }}</p>
            <p class="text-xs text-gray-500">Pendientes</p>
        </div>
        <div class="p-3 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $confirmedCount }}</p>
            <p class="text-xs text-gray-500">Confirmadas</p>
        </div>
    </div>

    <!-- Reservations List -->
    <div class="max-h-80 overflow-y-auto divide-y">
        @forelse($upcoming->take(5) as $reservation)
            <div class="p-3 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 flex items-center justify-center rounded-lg {{ match($reservation->status->value) {
                            'pending' => 'bg-yellow-100 text-yellow-600',
                            'confirmed' => 'bg-blue-100 text-blue-600',
                            default => 'bg-gray-100 text-gray-600'
                        } }}">
                            <span class="font-bold">{{ $reservation->reservation_time->format('H:i') }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $reservation->customer_name }}</p>
                            <div class="flex items-center gap-2 text-sm text-gray-500">
                                <span>{{ $reservation->party_size }} pers.</span>
                                @if($reservation->table)
                                    <span class="px-1.5 py-0.5 bg-purple-100 text-purple-700 rounded text-xs">
                                        Mesa {{ $reservation->table->number }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-1">
                        @if($reservation->status === \App\Enums\ReservationStatus::PENDING)
                            <button wire:click="quickConfirm({{ $reservation->id }})"
                                    class="p-1.5 bg-blue-100 hover:bg-blue-200 text-blue-600 rounded"
                                    title="Confirmar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                        @elseif($reservation->status === \App\Enums\ReservationStatus::CONFIRMED)
                            <button wire:click="quickSeat({{ $reservation->id }})"
                                    class="p-1.5 bg-green-100 hover:bg-green-200 text-green-600 rounded"
                                    title="Sentar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
                @if($reservation->special_requests)
                    <p class="mt-1 text-xs text-yellow-600 truncate">
                        {{ $reservation->special_requests }}
                    </p>
                @endif
            </div>
        @empty
            <div class="p-6 text-center text-gray-500">
                <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p>No hay reservaciones para hoy</p>
            </div>
        @endforelse
    </div>

    @if($upcoming->count() > 5)
        <div class="p-3 bg-gray-50 border-t text-center">
            <a href="{{ route('admin.reservations.index') }}" class="text-sm text-blue-600 hover:text-blue-700">
                Ver {{ $upcoming->count() - 5 }} más
            </a>
        </div>
    @endif
</div>
