<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click.self="closeCreateModal">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b bg-gradient-to-r from-blue-600 to-blue-700">
            <h3 class="text-xl font-bold text-white">Nueva Reservación</h3>
            <button wire:click="closeCreateModal" class="p-2 text-white/80 hover:text-white rounded-lg hover:bg-white/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Body -->
        <form wire:submit="createReservation" class="p-6 overflow-y-auto max-h-[70vh]">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Customer Info -->
                <div class="space-y-4">
                    <h4 class="font-semibold text-gray-800 border-b pb-2">Información del Cliente</h4>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                        <input type="text"
                               wire:model="customerName"
                               placeholder="Nombre completo"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('customerName') border-red-500 @enderror">
                        @error('customerName')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono *</label>
                        <input type="tel"
                               wire:model="customerPhone"
                               placeholder="Número de teléfono"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('customerPhone') border-red-500 @enderror">
                        @error('customerPhone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email"
                               wire:model="customerEmail"
                               placeholder="correo@ejemplo.com"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- Reservation Details -->
                <div class="space-y-4">
                    <h4 class="font-semibold text-gray-800 border-b pb-2">Detalles de la Reservación</h4>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha *</label>
                            <input type="date"
                                   wire:model.live="reservationDate"
                                   min="{{ now()->toDateString() }}"
                                   class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('reservationDate') border-red-500 @enderror">
                            @error('reservationDate')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hora *</label>
                            <select wire:model.live="reservationTime"
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @foreach($timeSlots as $slot)
                                    <option value="{{ $slot }}">{{ \Carbon\Carbon::parse($slot)->format('g:i A') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Personas *</label>
                            <input type="number"
                                   wire:model.live="partySize"
                                   min="1"
                                   max="50"
                                   class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Duración</label>
                            <select wire:model="duration"
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="60">1 hora</option>
                                <option value="90">1.5 horas</option>
                                <option value="120">2 horas</option>
                                <option value="180">3 horas</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mesa (opcional)</label>
                        <select wire:model="tableId"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Asignar automáticamente --</option>
                            @foreach($availableTables as $table)
                                <option value="{{ $table->id }}">
                                    Mesa {{ $table->number }} ({{ $table->capacity }} pers.) - {{ $table->zone->name }}
                                </option>
                            @endforeach
                        </select>
                        @if($availableTables->isEmpty() && $reservationDate && $reservationTime)
                            <p class="text-yellow-600 text-xs mt-1">No hay mesas disponibles con capacidad para {{ $partySize }} personas en este horario</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="mt-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Peticiones Especiales</label>
                    <textarea wire:model="specialRequests"
                              rows="2"
                              placeholder="Cumpleaños, alergias, silla para bebé, etc."
                              class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notas Internas</label>
                    <textarea wire:model="internalNotes"
                              rows="2"
                              placeholder="Notas visibles solo para el personal"
                              class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 mt-6 pt-4 border-t">
                <button type="button"
                        wire:click="closeCreateModal"
                        class="flex-1 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    Crear Reservación
                </button>
            </div>
        </form>
    </div>
</div>
