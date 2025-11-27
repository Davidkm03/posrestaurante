<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Pendientes</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['pending'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Confirmadas</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['confirmed'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Completadas</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['completed'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Canceladas</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['cancelled'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Controls -->
    <div class="bg-white rounded-xl shadow-sm border p-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <!-- View Mode Toggle -->
                <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                    <button wire:click="setViewMode('day')"
                            class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $viewMode === 'day' ? 'bg-white shadow text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                        Día
                    </button>
                    <button wire:click="setViewMode('week')"
                            class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $viewMode === 'week' ? 'bg-white shadow text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                        Semana
                    </button>
                </div>

                <!-- Date Navigation -->
                <div class="flex items-center gap-2">
                    @if($viewMode === 'day')
                        <button wire:click="previousDay" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                    @else
                        <button wire:click="previousWeek" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                    @endif

                    <button wire:click="goToToday" class="px-3 py-1.5 text-sm font-medium text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                        Hoy
                    </button>

                    <span class="text-base font-semibold text-gray-800 min-w-[220px] text-center">
                        {{ \Carbon\Carbon::parse($currentDate)->locale('es')->isoFormat('dddd, D [de] MMMM YYYY') }}
                    </span>

                    @if($viewMode === 'day')
                        <button wire:click="nextDay" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    @else
                        <button wire:click="nextWeek" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    @endif
                </div>
            </div>

            <!-- New Reservation Button -->
            <button wire:click="openCreateModal"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nueva Reservación
            </button>
        </div>
    </div>

    <!-- Calendar Content -->
    @if($viewMode === 'day')
        <!-- Day View -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            @if($reservations->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-gray-500">
                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-lg font-medium text-gray-600">No hay reservaciones para este día</p>
                    <p class="text-sm text-gray-400 mb-4">Crea una nueva reservación para comenzar</p>
                    <button wire:click="openCreateModal" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Nueva Reservación
                    </button>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Hora</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Personas</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Mesa</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Notas</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($reservations as $reservation)
                                <tr wire:click="selectReservation({{ $reservation->id }})"
                                    class="hover:bg-gray-50 cursor-pointer transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-semibold text-gray-900">
                                            {{ $reservation->reservation_time->format('g:i A') }}
                                        </span>
                                        <p class="text-xs text-gray-500">{{ $reservation->duration_minutes }} min</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="font-medium text-gray-600">{{ substr($reservation->customer_name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $reservation->customer_name }}</p>
                                                <p class="text-sm text-gray-500">{{ $reservation->customer_phone }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-1">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            <span class="text-gray-900">{{ $reservation->party_size }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($reservation->table)
                                            <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-lg text-sm font-medium">
                                                Mesa {{ $reservation->table->number }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-sm">Sin asignar</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-medium
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
                                    <td class="px-6 py-4">
                                        @if($reservation->special_requests)
                                            <p class="text-sm text-gray-600 truncate max-w-xs" title="{{ $reservation->special_requests }}">
                                                {{ $reservation->special_requests }}
                                            </p>
                                        @else
                                            <span class="text-gray-400 text-sm">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button wire:click.stop="selectReservation({{ $reservation->id }})" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @else
        <!-- Week View -->
        <div class="grid grid-cols-7 gap-3">
            @php
                $weekStart = \Carbon\Carbon::parse($currentDate)->startOfWeek();
            @endphp
            @for($i = 0; $i < 7; $i++)
                @php
                    $day = $weekStart->copy()->addDays($i);
                    $dayKey = $day->format('Y-m-d');
                    $dayReservations = $reservations[$dayKey] ?? collect();
                @endphp
                <div class="bg-white rounded-xl shadow-sm border {{ $day->isToday() ? 'ring-2 ring-blue-500' : '' }}">
                    <div class="p-3 border-b {{ $day->isToday() ? 'bg-blue-50' : 'bg-gray-50' }} rounded-t-xl">
                        <p class="text-xs font-medium text-gray-500 uppercase">{{ $day->locale('es')->isoFormat('ddd') }}</p>
                        <p class="text-xl font-bold {{ $day->isToday() ? 'text-blue-600' : 'text-gray-800' }}">
                            {{ $day->format('d') }}
                        </p>
                    </div>
                    <div class="p-2 space-y-2 min-h-[200px] max-h-[400px] overflow-y-auto">
                        @forelse($dayReservations as $reservation)
                            <button wire:click="selectReservation({{ $reservation->id }})"
                                    class="w-full text-left p-2.5 rounded-lg text-xs hover:shadow-md transition-all border-l-4
                                        {{ match($reservation->status->value) {
                                            'pending' => 'border-yellow-500 bg-yellow-50 hover:bg-yellow-100',
                                            'confirmed' => 'border-blue-500 bg-blue-50 hover:bg-blue-100',
                                            'seated' => 'border-green-500 bg-green-50 hover:bg-green-100',
                                            default => 'border-gray-300 bg-gray-50 hover:bg-gray-100'
                                        } }}">
                                <p class="font-bold text-gray-900">{{ $reservation->reservation_time->format('g:i A') }}</p>
                                <p class="text-gray-700 truncate font-medium">{{ $reservation->customer_name }}</p>
                                <p class="text-gray-500">{{ $reservation->party_size }} personas</p>
                            </button>
                        @empty
                            <div class="flex items-center justify-center h-20 text-gray-400 text-xs">
                                Sin reservaciones
                            </div>
                        @endforelse
                    </div>
                </div>
            @endfor
        </div>
    @endif

    <!-- Create Modal -->
    @if($showCreateModal)
        @include('livewire.reservations.partials.create-modal')
    @endif

    <!-- Detail Modal -->
    @if($showDetailModal && $selectedReservation)
        @include('livewire.reservations.partials.detail-modal')
    @endif
</div>
