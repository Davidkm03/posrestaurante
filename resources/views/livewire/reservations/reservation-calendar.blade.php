<div class="h-full flex flex-col">
    <!-- Header -->
    <div class="flex-shrink-0 bg-white border-b p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <h1 class="text-2xl font-bold text-gray-800">Reservaciones</h1>
                <div class="flex items-center gap-2 bg-gray-100 rounded-lg p-1">
                    <button wire:click="setViewMode('day')"
                            class="px-3 py-1 rounded text-sm font-medium transition-colors {{ $viewMode === 'day' ? 'bg-white shadow text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                        Día
                    </button>
                    <button wire:click="setViewMode('week')"
                            class="px-3 py-1 rounded text-sm font-medium transition-colors {{ $viewMode === 'week' ? 'bg-white shadow text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                        Semana
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <!-- Date Navigation -->
                <div class="flex items-center gap-2">
                    @if($viewMode === 'day')
                        <button wire:click="previousDay" class="p-2 hover:bg-gray-100 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                    @else
                        <button wire:click="previousWeek" class="p-2 hover:bg-gray-100 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                    @endif

                    <button wire:click="goToToday" class="px-3 py-1 text-sm font-medium text-blue-600 hover:bg-blue-50 rounded">
                        Hoy
                    </button>

                    <span class="text-lg font-semibold text-gray-800 min-w-[200px] text-center">
                        {{ \Carbon\Carbon::parse($currentDate)->locale('es')->isoFormat('dddd, D [de] MMMM') }}
                    </span>

                    @if($viewMode === 'day')
                        <button wire:click="nextDay" class="p-2 hover:bg-gray-100 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    @else
                        <button wire:click="nextWeek" class="p-2 hover:bg-gray-100 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    @endif
                </div>

                <!-- New Reservation Button -->
                <button wire:click="openCreateModal"
                        class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Nueva Reservación
                </button>
            </div>
        </div>

        <!-- Stats -->
        <div class="flex gap-6 mt-4 text-sm">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                <span class="text-gray-600">Pendientes: {{ $stats['pending'] ?? 0 }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                <span class="text-gray-600">Confirmadas: {{ $stats['confirmed'] ?? 0 }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-green-500"></span>
                <span class="text-gray-600">Completadas: {{ $stats['completed'] ?? 0 }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-red-500"></span>
                <span class="text-gray-600">Canceladas: {{ $stats['cancelled'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    <!-- Calendar Content -->
    <div class="flex-1 overflow-auto p-4">
        @if($viewMode === 'day')
            <!-- Day View -->
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                @if($reservations->isEmpty())
                    <div class="flex flex-col items-center justify-center py-16 text-gray-500">
                        <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-lg">No hay reservaciones para este día</p>
                        <button wire:click="openCreateModal" class="mt-4 text-blue-600 hover:text-blue-700">
                            + Crear nueva reservación
                        </button>
                    </div>
                @else
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Hora</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Cliente</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Personas</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Mesa</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Estado</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Notas</th>
                                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($reservations as $reservation)
                                <tr wire:click="selectReservation({{ $reservation->id }})"
                                    class="hover:bg-gray-50 cursor-pointer transition-colors">
                                    <td class="px-4 py-3">
                                        <span class="font-semibold text-gray-900">
                                            {{ $reservation->reservation_time->format('g:i A') }}
                                        </span>
                                        <p class="text-xs text-gray-500">{{ $reservation->duration_minutes }} min</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-900">{{ $reservation->customer_name }}</p>
                                        <p class="text-sm text-gray-500">{{ $reservation->customer_phone }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-gray-900">{{ $reservation->party_size }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($reservation->table)
                                            <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded text-sm">
                                                Mesa {{ $reservation->table->number }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-sm">Sin asignar</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded text-sm font-medium
                                            {{ match($reservation->status->value) {
                                                'pending' => 'bg-yellow-100 text-yellow-700',
                                                'confirmed' => 'bg-blue-100 text-blue-700',
                                                'seated' => 'bg-green-100 text-green-700',
                                                'completed' => 'bg-gray-100 text-gray-700',
                                                'cancelled' => 'bg-red-100 text-red-700',
                                                'no_show' => 'bg-orange-100 text-orange-700',
                                                default => 'bg-gray-100 text-gray-700'
                                            } }}">
                                            {{ $reservation->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($reservation->special_requests)
                                            <p class="text-sm text-gray-600 truncate max-w-xs" title="{{ $reservation->special_requests }}">
                                                {{ $reservation->special_requests }}
                                            </p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <button class="p-1 hover:bg-gray-200 rounded">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        @else
            <!-- Week View -->
            <div class="grid grid-cols-7 gap-2">
                @php
                    $weekStart = \Carbon\Carbon::parse($currentDate)->startOfWeek();
                @endphp
                @for($i = 0; $i < 7; $i++)
                    @php
                        $day = $weekStart->copy()->addDays($i);
                        $dayKey = $day->format('Y-m-d');
                        $dayReservations = $reservations[$dayKey] ?? collect();
                    @endphp
                    <div class="bg-white rounded-lg border {{ $day->isToday() ? 'ring-2 ring-blue-500' : '' }}">
                        <div class="p-2 border-b {{ $day->isToday() ? 'bg-blue-50' : 'bg-gray-50' }}">
                            <p class="text-xs text-gray-500">{{ $day->locale('es')->isoFormat('ddd') }}</p>
                            <p class="text-lg font-bold {{ $day->isToday() ? 'text-blue-600' : 'text-gray-800' }}">
                                {{ $day->format('d') }}
                            </p>
                        </div>
                        <div class="p-2 space-y-1 min-h-[200px] max-h-[400px] overflow-y-auto">
                            @forelse($dayReservations as $reservation)
                                <button wire:click="selectReservation({{ $reservation->id }})"
                                        class="w-full text-left p-2 rounded text-xs hover:bg-gray-100 border-l-4
                                            {{ match($reservation->status->value) {
                                                'pending' => 'border-yellow-500 bg-yellow-50',
                                                'confirmed' => 'border-blue-500 bg-blue-50',
                                                'seated' => 'border-green-500 bg-green-50',
                                                default => 'border-gray-300 bg-gray-50'
                                            } }}">
                                    <p class="font-semibold text-gray-900">{{ $reservation->reservation_time->format('g:i A') }}</p>
                                    <p class="text-gray-600 truncate">{{ $reservation->customer_name }}</p>
                                    <p class="text-gray-500">{{ $reservation->party_size }} pers.</p>
                                </button>
                            @empty
                                <p class="text-gray-400 text-xs text-center py-4">Sin reservaciones</p>
                            @endforelse
                        </div>
                    </div>
                @endfor
            </div>
        @endif
    </div>

    <!-- Create Modal -->
    @if($showCreateModal)
        @include('livewire.reservations.partials.create-modal')
    @endif

    <!-- Detail Modal -->
    @if($showDetailModal && $selectedReservation)
        @include('livewire.reservations.partials.detail-modal')
    @endif
</div>
