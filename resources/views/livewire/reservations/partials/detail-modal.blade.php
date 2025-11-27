<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" wire:click.self="closeDetailModal">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden transform transition-all animate-modal-in">
        <!-- Header -->
        <div class="relative p-6 {{ match($selectedReservation->status->value) {
            'pending' => 'bg-gradient-to-r from-yellow-500 to-amber-500',
            'confirmed' => 'bg-gradient-to-r from-blue-500 to-indigo-500',
            'seated' => 'bg-gradient-to-r from-green-500 to-emerald-500',
            'completed' => 'bg-gradient-to-r from-gray-500 to-slate-500',
            'cancelled' => 'bg-gradient-to-r from-red-500 to-rose-500',
            'no_show' => 'bg-gradient-to-r from-orange-500 to-amber-600',
            default => 'bg-gradient-to-r from-gray-500 to-slate-500'
        } }}">
            <button wire:click="closeDetailModal" class="absolute top-4 right-4 p-2 text-white/80 hover:text-white rounded-xl hover:bg-white/20 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center text-white text-xl font-bold">
                    {{ substr($selectedReservation->customer_name, 0, 1) }}
                </div>
                <div>
                    <p class="text-white/70 text-sm">Reservación #{{ $selectedReservation->id }}</p>
                    <h3 class="text-xl font-bold text-white">{{ $selectedReservation->customer_name }}</h3>
                    <span class="inline-flex items-center gap-1.5 mt-1 px-3 py-1 bg-white/20 text-white rounded-full text-sm font-medium">
                        <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                        {{ $selectedReservation->status_label }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-5">
            <!-- Date & Time Card -->
            <div class="flex items-center gap-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-100">
                <div class="w-14 h-14 flex items-center justify-center bg-blue-100 text-blue-600 rounded-xl">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-900">
                        {{ $selectedReservation->reservation_date->locale('es')->isoFormat('dddd, D [de] MMMM') }}
                    </p>
                    <p class="text-gray-600 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $selectedReservation->reservation_time->format('g:i A') }} -
                        {{ $selectedReservation->end_time->format('g:i A') }}
                        <span class="text-gray-400 text-sm">({{ $selectedReservation->duration_minutes }} min)</span>
                    </p>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-gray-50 rounded-xl">
                    <div class="flex items-center gap-2 text-gray-500 text-sm mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Personas
                    </div>
                    <p class="font-bold text-lg text-gray-900">{{ $selectedReservation->party_size }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <div class="flex items-center gap-2 text-gray-500 text-sm mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        Mesa
                    </div>
                    <p class="font-bold text-lg text-gray-900">
                        @if($selectedReservation->table)
                            Mesa {{ $selectedReservation->table->number }}
                        @else
                            <span class="text-gray-400 font-normal">Sin asignar</span>
                        @endif
                    </p>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <div class="flex items-center gap-2 text-gray-500 text-sm mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        Teléfono
                    </div>
                    <p class="font-bold text-gray-900">{{ $selectedReservation->customer_phone }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <div class="flex items-center gap-2 text-gray-500 text-sm mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Email
                    </div>
                    <p class="font-bold text-gray-900 truncate">{{ $selectedReservation->customer_email ?? '-' }}</p>
                </div>
            </div>

            @if($selectedReservation->special_requests)
                <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl">
                    <div class="flex items-center gap-2 text-amber-700 font-semibold text-sm mb-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        Peticiones Especiales
                    </div>
                    <p class="text-amber-800">{{ $selectedReservation->special_requests }}</p>
                </div>
            @endif

            @if($selectedReservation->internal_notes)
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl">
                    <div class="flex items-center gap-2 text-slate-600 font-semibold text-sm mb-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Notas Internas
                    </div>
                    <p class="text-slate-700">{{ $selectedReservation->internal_notes }}</p>
                </div>
            @endif

            <!-- Timestamps -->
            <div class="flex items-center justify-between text-xs text-gray-400 pt-3 border-t border-gray-100">
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Creada: {{ $selectedReservation->created_at->format('d/m/Y H:i') }}
                </span>
                @if($selectedReservation->confirmed_at)
                    <span class="flex items-center gap-1 text-green-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Confirmada: {{ $selectedReservation->confirmed_at->format('d/m/Y H:i') }}
                    </span>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="p-5 border-t bg-gray-50 space-y-3">
            @switch($selectedReservation->status->value)
                @case('pending')
                    <div class="grid grid-cols-2 gap-3">
                        <button wire:click="confirmReservation"
                                class="py-3 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition-all shadow-lg shadow-blue-500/30 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Confirmar
                        </button>
                        <button wire:click="cancelReservation"
                                wire:confirm="¿Cancelar esta reservación?"
                                class="py-3 px-4 bg-red-50 hover:bg-red-100 text-red-700 font-semibold rounded-xl transition-all border border-red-200 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancelar
                        </button>
                    </div>
                    @break

                @case('confirmed')
                    <div class="grid grid-cols-2 gap-3">
                        <button wire:click="seatReservation"
                                class="py-3 px-4 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-xl transition-all shadow-lg shadow-green-500/30 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Sentar Cliente
                        </button>
                        <button wire:click="markNoShow"
                                wire:confirm="¿Marcar como no asistió?"
                                class="py-3 px-4 bg-orange-50 hover:bg-orange-100 text-orange-700 font-semibold rounded-xl transition-all border border-orange-200 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            No Asistió
                        </button>
                    </div>
                    <button wire:click="cancelReservation"
                            wire:confirm="¿Cancelar esta reservación?"
                            class="w-full py-2.5 px-4 bg-red-50 hover:bg-red-100 text-red-600 font-medium rounded-xl transition-all border border-red-200 flex items-center justify-center gap-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancelar Reservación
                    </button>
                    @break

                @case('seated')
                    <div class="text-center py-3 px-4 bg-green-50 text-green-700 font-semibold rounded-xl border border-green-200 flex items-center justify-center gap-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                        Cliente en mesa actualmente
                    </div>
                    @break

                @default
                    <div class="text-center py-3 px-4 bg-gray-100 text-gray-600 font-medium rounded-xl">
                        Reservación {{ $selectedReservation->status_label }}
                    </div>
            @endswitch

            <button wire:click="closeDetailModal"
                    class="w-full py-3 px-4 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-xl transition-all flex items-center justify-center gap-2">
                Cerrar
            </button>
        </div>
    </div>
</div>

<style>
    @keyframes modal-in {
        from { opacity: 0; transform: scale(0.95) translateY(-10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .animate-modal-in {
        animation: modal-in 0.2s ease-out forwards;
    }
</style>
