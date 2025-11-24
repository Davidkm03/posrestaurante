<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click.self="closeDetailModal">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
        <!-- Header -->
        <div class="p-4 border-b {{ match($selectedReservation->status->value) {
            'pending' => 'bg-yellow-500',
            'confirmed' => 'bg-blue-500',
            'seated' => 'bg-green-500',
            'completed' => 'bg-gray-500',
            'cancelled' => 'bg-red-500',
            'no_show' => 'bg-orange-500',
            default => 'bg-gray-500'
        } }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/80 text-sm">Reservación</p>
                    <h3 class="text-xl font-bold text-white">{{ $selectedReservation->customer_name }}</h3>
                </div>
                <div class="text-right">
                    <span class="px-3 py-1 bg-white/20 text-white rounded-full text-sm font-medium">
                        {{ $selectedReservation->status_label }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-4">
            <!-- Date & Time -->
            <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                <div class="w-12 h-12 flex items-center justify-center bg-blue-100 text-blue-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-lg font-semibold text-gray-900">
                        {{ $selectedReservation->reservation_date->locale('es')->isoFormat('dddd, D [de] MMMM') }}
                    </p>
                    <p class="text-gray-600">
                        {{ $selectedReservation->reservation_time->format('g:i A') }} -
                        {{ $selectedReservation->end_time->format('g:i A') }}
                        <span class="text-gray-400">({{ $selectedReservation->duration_minutes }} min)</span>
                    </p>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Personas</p>
                    <p class="font-semibold text-gray-900">{{ $selectedReservation->party_size }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Mesa</p>
                    <p class="font-semibold text-gray-900">
                        @if($selectedReservation->table)
                            Mesa {{ $selectedReservation->table->number }}
                        @else
                            <span class="text-gray-400">Sin asignar</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Teléfono</p>
                    <p class="font-semibold text-gray-900">{{ $selectedReservation->customer_phone }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="font-semibold text-gray-900">{{ $selectedReservation->customer_email ?? '-' }}</p>
                </div>
            </div>

            @if($selectedReservation->special_requests)
                <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm font-medium text-yellow-800">Peticiones Especiales:</p>
                    <p class="text-yellow-700">{{ $selectedReservation->special_requests }}</p>
                </div>
            @endif

            @if($selectedReservation->internal_notes)
                <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                    <p class="text-sm font-medium text-gray-600">Notas Internas:</p>
                    <p class="text-gray-700">{{ $selectedReservation->internal_notes }}</p>
                </div>
            @endif

            <!-- Timestamps -->
            <div class="text-xs text-gray-400 pt-2 border-t">
                <p>Creada: {{ $selectedReservation->created_at->format('d/m/Y H:i') }}</p>
                @if($selectedReservation->confirmed_at)
                    <p>Confirmada: {{ $selectedReservation->confirmed_at->format('d/m/Y H:i') }}</p>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="p-4 border-t bg-gray-50 space-y-2">
            @switch($selectedReservation->status->value)
                @case('pending')
                    <div class="grid grid-cols-2 gap-2">
                        <button wire:click="confirmReservation"
                                class="py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg">
                            Confirmar
                        </button>
                        <button wire:click="cancelReservation"
                                wire:confirm="¿Cancelar esta reservación?"
                                class="py-2 bg-red-100 hover:bg-red-200 text-red-700 font-medium rounded-lg">
                            Cancelar
                        </button>
                    </div>
                    @break

                @case('confirmed')
                    <div class="grid grid-cols-2 gap-2">
                        <button wire:click="seatReservation"
                                class="py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg">
                            Sentar Cliente
                        </button>
                        <button wire:click="markNoShow"
                                wire:confirm="¿Marcar como no asistió?"
                                class="py-2 bg-orange-100 hover:bg-orange-200 text-orange-700 font-medium rounded-lg">
                            No Asistió
                        </button>
                    </div>
                    <button wire:click="cancelReservation"
                            wire:confirm="¿Cancelar esta reservación?"
                            class="w-full py-2 bg-red-100 hover:bg-red-200 text-red-700 font-medium rounded-lg">
                        Cancelar Reservación
                    </button>
                    @break

                @case('seated')
                    <p class="text-center text-green-600 font-medium py-2">
                        Cliente en mesa
                    </p>
                    @break

                @default
                    <p class="text-center text-gray-500 py-2">
                        Reservación {{ $selectedReservation->status_label }}
                    </p>
            @endswitch

            <button wire:click="closeDetailModal"
                    class="w-full py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg mt-2">
                Cerrar
            </button>
        </div>
    </div>
</div>
